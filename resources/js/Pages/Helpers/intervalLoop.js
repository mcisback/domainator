/** Make A Loop Interval */
export function intervalLoop(records, callback, time) {
    let count = 0

    const interval = setInterval(() => {
        if(count >= (records.length - 1)) {
            console.log('records: ', records)

            clearInterval(interval)
        } else {
            console.log(`records[${count}]: `, records[count])

            if(!!records[count]) {
                callback(records[count])
            }

            count++
        }
    }, time)
}
