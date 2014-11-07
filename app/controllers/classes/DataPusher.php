// abstract. looks at a document and either moves the document to the next most available queue or puts in the database


abstract public storedocument : looks at what transfomrations still need to be run.  puts the document in the queue with the least work on it or puts in in db

private putdocinqueue(string queuename)

private putdocindb(details about where/how to put it)
