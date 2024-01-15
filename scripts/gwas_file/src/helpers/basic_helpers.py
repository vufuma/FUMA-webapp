##### detect file delimiter from the header #####
def DetectDelim(header):
	if re.match(r'.*\s\s.*', header) is not None:
		return r'\s+'
	sniffer = csv.Sniffer()
	dialect = sniffer.sniff(header)
	return dialect.delimiter

# this function is used to check if a variable is a float
def is_float(s):
    return isinstance(s, float)