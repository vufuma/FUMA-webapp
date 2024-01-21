from dataclasses import dataclass

@dataclass
class Column:
	name: str
	hardcoded_name: str
	regex: str = None
	index: int = None
	found: bool = False