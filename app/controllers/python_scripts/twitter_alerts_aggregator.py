# Accepts a json of the following format:

# {
#     _id: "text...",
#     id: [502573575896043500],
#     future_time_norm: "2014-08-22 00:52",
#     future_time_original: "tomorrow",
#     location: "Jackson square",
#     location_type: "Facility",
#     retweet_count: 1
# },


import pandas as pd
import numpy as np
import json
import re
import sys

input_file = sys.argv[1]
output_file = sys.argv[2]


file = open(input_file)
json = json.load(file)

def make_partial_tweet(text):
    # remove highly variable strings (hashtags, usernames, urls...) from the tweet so that groupby is more successfull
    text = text.strip().split(' ')
    text_new = []
    regex = re.compile('[@#]|(http)|(RT)')
    for token in text:
        if (not regex.search(token)):
            text_new.append(token)
    return ' '.join(text_new).lower()[:50]

def consolidate_times(time_array):
    # takes all set of aggregated times from the json input file and picks the highest time and highest hour
    dates, times, datetimes = [""], [""], [""]
    for item in time_array:
        if (len(item) == 10) :
            dates.append(item)
        elif (len(item) == 5):
            times.append(item)
        elif (len(item) == 16):
            datetimes.append(item)
    if (len(datetimes) > 1):
        return max(datetimes)
    else:
        return str(max(dates)) + ' ' + str(min(times))
        
def fix_time(time):
    # remove the time from the datetimes that arent round to the half hour.  Those times are estimated incorrectly.
    if (len(time) == 16):
        if (time[14:] != "00" and time[14:] != "30"):
            time = time[:10]
    return time

    
    
text = [item['_id'] for item in json]
partial_text = [make_partial_tweet(item) for item in text]
location = [', '.join(item['location']) for item in json]
location_type = [', '.join(item['location_type']) for item in json]
future_time_norm = [fix_time(consolidate_times(item['future_time_norm'])).strip() for item in json]
future_time_original = [item['future_time_original'] for item in json]
retweet_count = [item['retweet_count']+1 for item in json]
ids = [item['id'] for item in json]


df = pd.DataFrame({ 'text' : text, 
                    'partial_text' : partial_text,
                    'location' : location, 
                    'location_type' : location_type,
                    'future_time_norm' : future_time_norm,
                    'future_time_original' : future_time_original,                    
                    'ids' : ids,
                    'retweet_count' : retweet_count})
df = df.sort(['partial_text', 'location'], ascending=False)
df = df[df.future_time_norm.str.len() >= 10] # remove lines where date time only includes YYYY-MM.

df_text = df[['partial_text', 'text']].groupby('partial_text').first()
df_loc_time = df[['partial_text', 'location', 'location_type', 'future_time_norm']].groupby('partial_text').min()
df_ids = df[['partial_text', 'ids']].groupby('partial_text').sum()
df_ids.ids = [list(set(item)) for item in df_ids.ids]
df_retweet_count = df[['partial_text', 'retweet_count']].groupby('partial_text').sum()

df = pd.concat([df_text, df_loc_time, df_ids, df_retweet_count], axis=1).sort('retweet_count', ascending=False)

df.to_json(output_file, orient="records")    

