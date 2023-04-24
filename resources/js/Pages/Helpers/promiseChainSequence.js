/** Make A Loop Interval */
export async function promiseChainSequence(records, callback) {
    // let count = 0

    const result = [];

    await records.reduce(async (promise, item) => promise
        .then((res) => callback(item, res))
        .then((res) => result.push(res)
        ), Promise.resolve());

    return result;
}
