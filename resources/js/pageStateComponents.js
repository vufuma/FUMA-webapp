// The root class for state objects
// to share global page state the major
// pages in the application.
class PageState {
    name;
    constructor(property_array) {
        this.value = new Map (property_array)
    }

    set(key, value) {
        this.validate(key);
        this.value.set(key, value);
    }
    get(key) {
        this.validate(key)
        return this.value.get(key);
    }
    validate(key) {
        if (!this.value.has(key)) {
            throw new RangeError(`Runtime error - invalid ${this.constructor.name} key: ${key}`)
        }
    }
}

// Each instantiated state is a module global singleton
// in which the defined property set can be modified
// and retrieved.

export const S2GPageState = new PageState([
        ["subdir", ""],
        ["status", ""],
        ["id", ""],
        ["page", ""],
        ["loggedin", ""]
    ]);

S2GPageState.setState = function(
    subdir,
    status,
    id,
    page,
    loggedin, 
) {
    this.set('subdir', subdir);
    this.set('status', status);
    this.set('id', id);
    this.set('page', page);
    this.set('loggedin', loggedin);
}

export const G2FPageState = new PageState ([
        ["public_path", ""],
        ["storage_path", ""],
        ["subdir", ""],
        ["jobdir", ""],
        ["status", ""],
        ["id", ""],
        ["page", ""],
        ["loggedin", ""],
        ["prefix", ""] 
    ]);

G2FPageState.setState = function(
	public_path, 
	storage_path,
	subdir,
	jobdir,
	status,
	id,
	page,
	loggedin,
	prefix
) {
	this.set("public_path", public_path);
	this.set("storage_path", storage_path)
	this.set("subdir", subdir);
	this.set("jobdir", jobdir);
	this.set("status", status);
	this.set("id", id);
	this.set("page", page);
	this.set("loggedin", loggedin);
	this.set("prefix", prefix);
}

export const CellTypeState = new PageState ([
        ["status", ""],
        ["id", ""],
        ["prefix", ""],
        ["page", ""],
        ["subdir", ""],
        ["loggedin", ""]
    ]);

CellTypeState.setState = function(
    status,
    id,
    prefix,
    page,
    subdir,
    loggedin, 
) {
    this.set('status', status);
    this.set('id', id);
    this.set('prefix', prefix);
    this.set('page', page);
    this.set('subdir', subdir);
    this.set('loggedin', loggedin);
}

export const BrowsePageState = new PageState ([
        ["id", ""],
        ["page", ""],
        ["subdir", ""],
        ["loggedin", ""]
    ]);

BrowsePageState.setState = function(
    id,
    page,
    subdir,
    loggedin, 
) {
    this.set('id', id);
    this.set('page', page);
    this.set('subdir', subdir);
    this.set('loggedin', loggedin);
}

export const AnnotPlotPageState = new PageState ([
    ["id", ""],
    ["page", ""],
    ["subdir", ""],
    ["loggedin", ""]
]);

AnnotPlotPageState.setState = function(
    id,
    page,
    subdir,
    loggedin, 
) {
    this.set('id', id);
    this.set('page', page);
    this.set('subdir', subdir);
    this.set('loggedin', loggedin);
}