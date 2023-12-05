# %%
import mysql.connector
import pandas as pd
import numpy as np
import os
import subprocess
import time

# %%
new_share_jobs="/new_data/users_data"
old_share_jobs="/data/fuma"

# %%
mydb = mysql.connector.connect(
  host="localhost",
  port="3306",
  user="root",
  password="root",
  database="fuma_new",
)

print(mydb)

# %%
mydb.connect()
mycursor = mydb.cursor()
mycursor.execute("SELECT jobID, old_id, type, is_public, parent_id FROM SubmitJobs")

jobs = []
for x in mycursor:
  jobs.append(x)

jobs_df = pd.DataFrame(jobs, columns =['jobID', 'old_id', 'type', 'is_public', 'parent_id'])
jobs_df["location"] = np.nan
jobs_df["destination"] = np.nan

# jobs_df.drop(jobs_df.query("old_id.isna()").index, inplace=True)
# jobs_df

# %%
filtered_indices = jobs_df.query("type == 'snp2gene' and is_public == 0").index
jobs_df.loc[filtered_indices, 'location'] = jobs_df.loc[filtered_indices, 'jobID'].apply(lambda x: old_share_jobs + '/jobs/' + str(int(x)))
jobs_df.loc[filtered_indices, 'destination'] = jobs_df.loc[filtered_indices, 'jobID'].apply(lambda x: new_share_jobs + '/jobs/' + str(int(x)))

filtered_indices = jobs_df.query("type == 'celltype' and is_public == 0").index
jobs_df.loc[filtered_indices, 'location'] = jobs_df.loc[filtered_indices, 'old_id'].apply(lambda x: old_share_jobs + '/celltype/' + str(int(x))) # add int
jobs_df.loc[filtered_indices, 'destination'] = jobs_df.loc[filtered_indices, 'jobID'].apply(lambda x: new_share_jobs + '/celltype/' + str(int(x)))

filtered_indices = jobs_df.query("is_public == 1").index
jobs_df.loc[filtered_indices, 'location'] = jobs_df.loc[filtered_indices, 'old_id'].apply(lambda x: old_share_jobs + '/public/' + str(int(x))) # add int
jobs_df.loc[filtered_indices, 'destination'] = jobs_df.loc[filtered_indices, 'jobID'].apply(lambda x: new_share_jobs + '/jobs/' + str(int(x)))

# split gene2func into multiple senarios
# 1) standalone gene2func jobs ("type == 'gene2func' and parent_id == NaN")
filtered_indices = jobs_df.query("type == 'gene2func' and parent_id.isna()").index
jobs_df.loc[filtered_indices, 'location'] = jobs_df.loc[filtered_indices, 'old_id'].apply(lambda x: old_share_jobs + '/gene2func/' + str(int(x))) # add int
jobs_df.loc[filtered_indices, 'destination'] = jobs_df.loc[filtered_indices, 'jobID'].apply(lambda x: new_share_jobs + '/gene2func/' + str(int(x)))


# 2) gene2func jobs that are child of snp2gene jobs ("type == 'gene2func' and parent_id != NaN)
# for each one of these jobs, we need to check if the parent snp2gene job is public or not
# if not public, then we need to just copy the job to the new share
# if public, then we need to copy the job from public to the new share given the parent_id
filtered_indices = jobs_df.query("type == 'gene2func' and parent_id.notna()").index

for index in filtered_indices:
    parent_id = jobs_df.loc[index, 'parent_id']

    parent_job = jobs_df.query("jobID == @parent_id")

    if parent_job['is_public'].values[0] == 0:
        jobs_df.at[index, 'location'] = old_share_jobs + '/gene2func/' + str(int(jobs_df.loc[index, 'old_id'])) 
        jobs_df.at[index, 'destination'] = new_share_jobs + '/gene2func/' + str(int(jobs_df.loc[index, 'jobID']))
        # pass
    elif parent_job['is_public'].values[0] == 1:
        jobs_df.at[index, 'location'] = old_share_jobs + '/public/' + str(int(parent_job['old_id'].values[0])) + '/g2f' # add int
        jobs_df.at[index, 'location_alt'] = old_share_jobs + '/gene2func/' + str(int(jobs_df.loc[index, 'old_id'])) # add int
        jobs_df.at[index, 'destination'] = new_share_jobs + '/gene2func/' + str(int(jobs_df.loc[index, 'jobID']))
        # print(jobs_df.at[index, 'location'])
        # print(jobs_df.loc[index])
        # break

# %%
# iterate through the dataframe in for loop one line at a time
os.system("mkdir " + new_share_jobs + "/jobs")
os.system("mkdir " + new_share_jobs + "/celltype")
os.system("mkdir " + new_share_jobs + "/gene2func")

time.sleep(2);

for index, row in jobs_df.iterrows():
    one_location_at_least_exists = False
    if os.path.isdir(row['location']):
        location = row['location']
        jobs_df.at[index, 'copying_status'] = 'location exists,'
        one_location_at_least_exists = True
    if pd.notna(row['location_alt']) and os.path.isdir(row['location_alt']):
        location = row['location_alt']
        jobs_df.at[index, 'copying_status'] = str(jobs_df.at[index, 'copying_status']) + 'alt location exists'
        one_location_at_least_exists = True
    if one_location_at_least_exists:
        # os.system("rsync -rtlHv " + location + " " + row['destination'])

        result = subprocess.run("rsync -rtlHv " + location + " " + row['destination'],
                    shell=True, capture_output=True,
                    check=True, text=True)
        
        jobs_df.at[index, 'stdout'] = result.stdout
        jobs_df.at[index, 'stderr'] = result.stderr
    else:
        jobs_df.at[index, 'copying_status'] = 'neither location exists'

    

# %%
jobs_df.to_csv("result/orthogonal_jobs_table.csv",index = False)


