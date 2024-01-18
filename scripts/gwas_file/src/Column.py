from dataclasses import dataclass

@dataclass
class Column:
	name: str
	regex: str = None
	index: int = None
	found: bool = False