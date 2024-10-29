const G2FPageState = {
    value : new Map ([
        ["public_path", ""],
        ["storage_path", ""],
        ["subdir", ""],
        ["jobdir", ""],
        ["status", ""],
        ["id", ""],
        ["page", ""],
        ["loggedin", ""],
        ["prefix", ""] 
    ]),
    set(key, value) {
        this.validate(key);
        this.value.set(key, value);
    },
    get(key) {
        this.validate(key)
        return this.value.get(key);
    },
    validate(key) {
        if (!this.value.has(key)) {
            throw new RangeError(`Runtime error - invalid G2FPageState key: ${key}`)
        }
    }

}
export default G2FPageState;