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

latent_factor = 24 # HOW FAR BACK TO START COLLECTING THE CORPUS.  DIRECTLY AFFECTS HOW SOON THE TREND STARTS TO PLATEAU OR DECLINE.
global_corpus_size = 24 # THE TF CORPUS WILL ALWAYS CONTAIN ONE UNIT'S WORTH OF INFORMATION (E.G. ONE HOUR, ONE DAY), THIS WILL DETERMINE HOW MANY UNITS TO USE FOR THE ENTIRE TFIDF CORPUS.  DIRECTLY AFFECTS THE SMOOTHNES OF THE GRAPH AND COMPUTATIONAL RESOURCES.
N = 24 # THE NUMBER OF DATA POINTS TO DISPLAY ON THE GRAPH. 
num_features_to_plot = 5
windows = {'hour' : 'hour', 'day' : 'day'}
window = windows['hour']

file = open(input_file)
json = json.load(file)

id = [item["_id"] for item in json]
datetime = [item["datetime"] for item in json]
text = [' '.join(item["text"]) for item in json]
df = pd.DataFrame({'id' : id,
                   'datetime' : datetime,
                   'text' : text})

def group_df_into_windows (df, window):
    if (window == 'hour'):
        datetime_hour = []
        for i, item in enumerate(df.index):
            datetime_hour.append(df.datetime[i][:13])
        df['datetime_hour'] = datetime_hour
        df = df[['datetime_hour', 'text']]
        df = df.groupby('datetime_hour', as_index=False).sum().sort('datetime_hour', ascending=False)       
    return df

df = group_df_into_windows(df, window)



vectorizer = CountVectorizer(max_df=1.0, min_df=1, ngram_range=(1, 1), stop_words='english')
vectorized_texts = vectorizer.fit_transform(df.text)



def make_scores_matrix(vectorized_texts):
    '''
    creates a TFIDF matrix out of the vectorized text of a single timepoint(one row), and the vectorized texts at position 
    timepoint + latent_factor (global_corpus_size rows).  This gives a matrix with the timepoint of interest, and several
    additional timepoints for the global tfidf weights.  This matrix is then transformed into a TFIDF matrix, and the first
    vector (our timepoint), is appended to the results list.
    '''
    try:
        results = []
        for i in range(N):
            sub_matrix1 = vectorized_texts[i]
            sub_matrix2 = vectorized_texts[i+latent_factor : i+latent_factor+global_corpus_size]
            matrix_temp = vstack([sub_matrix1, sub_matrix2])
            tfidf = TfidfTransformer().fit_transform(matrix_temp).toarray()
            results.append(tfidf[0])
    except IndexError:
        raise Exception('Not enough timepoints.  Select smaller range or adjust parameters.')
    return results

final_scores_matrix = make_scores_matrix(vectorized_texts)

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

y_vectors_dict = get_features_to_plot(final_scores_matrix, num_features_to_plot)
# x_vector = df['datetime_hour'][:N].apply(lambda x: str(x))



for feature in y_vectors_dict:
    plt.plot(range(N), y_vectors_dict[feature], label=feature)

plt.ylim((0,1))
plt.legend(loc='upper left')
plt.savefig(output_file)