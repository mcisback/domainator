/** Make A Loop Interval */
export function intervalLoop(records, callback, time) {
    let count = 0

    const interval = setInterval(() => {
        if(count < records.length) {
            console.log(`${records.length},records[${count}]: `, records[count])

            if(!!records[count]) {
                callback(records[count])
            }

            count++
        } else {
            console.log('records: ', records)

            clearInterval(interval)
        }
    }, time)
}
