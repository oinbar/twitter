// Abstract. Extends DataPusher. Takes a document and runs the particular transformation.

Throws TransformationException

constructor(document)

fields:
	string queue_to_fetch_from
	string creates_field
	string list depends_on_prev_transforms (make sure to avoid cycles)

Methods:
	fetchdoc : 
	abstract transform (string field_name_to_act_on) : performs specific transformation, adding the correct field
	signoff : signs off the particular transfomration adding datetime
