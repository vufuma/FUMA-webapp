import re
import csv

# This function is used to detect the delimiter of a CSV file.
# The function takes a header parameter, which is assumed to be the first row of a CSV file.
# It uses a regular expression (re.match) to check if there are two or more consecutive whitespace characters in the header.
# If this condition is true, it returns the regular expression r'\s+', which represents one or more whitespace characters. This suggests that the function is assuming the file is space-separated.
# If the first condition is not met, it uses the csv.Sniffer() class to automatically detect the delimiter in the CSV file.
# The sniffer.sniff(header) call analyzes the input header and returns a Dialect object that contains information about the CSV format, including the detected delimiter.
# Finally, it returns the detected delimiter using dialect.delimiter.
def DetectDelim(header):
	if re.match(r'.*\s\s.*', header) is not None:
		return r'\s+'
	sniffer = csv.Sniffer()
	dialect = sniffer.sniff(header)
	return dialect.delimiter

# this function is used to check if a variable is a float
def is_float(s):
    return isinstance(s, float)