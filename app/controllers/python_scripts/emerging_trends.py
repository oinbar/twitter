# Emerging trends, also a more complex version of simple "Trends". Simple Trends is like emerging trends,
# only that the TF (background) is a uniform distribution.

# Accepts a json of the following format:

# {
#     _id: "id",
#     datetime: "datetime",
#     text: "text...",
# },

import json
import pandas as pd
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.feature_extraction.text import TfidfTransformer
from scipy.sparse import coo_matrix, vstack
import numpy as np
import matplotlib.pyplot as plt
import sys


input_file = sys.argv[1]
output_file = sys.argv[2]
num_features = sys.argv[3]
timeframe = sys.argv[4]

num_features_to_plot = 5
timeframes = {'hour' : 'hour', 'day' : 'day'}

file = open(input_file)
json = json.load(file)

id = [item["_id"] for item in json]
datetime = [item["datetime"] for item in json]
text = [' '.join(item["text"]) for item in json]
df = pd.DataFrame({'id' : id,
                   'datetime' : datetime,
                   'text' : text})

def group_df_into_windows (df, timeframe):
    window = []
    for i, item in enumerate(df.index):
        if (timeframe == 'hour'):
            window.append(df.datetime[i][:13])
        elif (timeframe == 'day'):
            window.append(df.datetime[i][:10])        
    df['window'] = window
    df = df[['window', 'text']]
    df = df.groupby('window').apply(lambda x: ' '.join(x.text)) #.sort('window', ascending=False)
    df = pd.DataFrame(df)
    df = pd.DataFrame({'window' : df.index, 'text' : df[0] }).reset_index(drop=True)            
    return df

df = group_df_into_windows(df, timeframe)


vectorizer = CountVectorizer(max_df=1.0, min_df=1, ngram_range=(1, 1), stop_words='english')
vectorized_texts = vectorizer.fit_transform(df.text)

tfidf_scores = TfidfTransformer().fit_transform(vectorized_texts).toarray()


def get_features_to_plot(final_scores_matrix, num_features_to_plot, selection_method = None):
    '''
    Takes a scores matrix comprising of vectors of features at timepoints.  This function uses a selection method to
    find the top n best features to plot.  It returns a dictionary, where each key is a feature name, and the value is
    the corresponding y-data vector.
    '''
    plot_data = {}
    
    # TOP AVERAGES
    averages = []
    for i in range(len(final_scores_matrix[0])):
        averages.append(np.mean([final_scores_matrix[j][i] for j in range(len(final_scores_matrix))]))

    threshold = sorted(averages, reverse=True)[num_features_to_plot-1]

    indices = []
    for i in range(num_features_to_plot):
        for j in range(len(averages)):
            if (averages[j] >= threshold):
                indices.append(j)

    transposed_matrix = zip(*final_scores_matrix)
    features = vectorizer.get_feature_names()
    plot_data = {features[i]:transposed_matrix[i] for i in indices}  
    return plot_data

y_vectors_dict = get_features_to_plot(tfidf_scores, num_features_to_plot)
x_vector = df.window.apply(lambda x: str(x))

for feature in y_vectors_dict:
    plt.plot(range(len(y_vectors_dict[feature])), y_vectors_dict[feature], label=feature)
    plt.xticks(range(len(x_vector)), x_vector, rotation=45)

plt.ylim((0,1))
plt.legend(loc='best')
plt.savefig(output_file, format='jpg')