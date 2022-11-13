<script>

    import moment from "moment";

    export let countDownDate = null
    countDownDate = new Date(countDownDate)

    let hours = 0
    let minutes = 0
    let seconds = 0
    let dateDiff = 0

    const interval = setInterval(() => {
        const diff = countDownDate.getTime() - Date.now()
        const days = Math.floor(diff / (1000 * 60 * 60 * 24))

        // console.log('days: ', {days, countDownDate})

        hours = days * 24 + Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
        minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
        seconds = Math.floor((diff % (1000 * 60)) / 1000)

        if(diff <= 0) {
            dateDiff = 'INIZIATO'
        } else {
            // dateDiff = moment(diff).format('hh:mm:ss')
            dateDiff = `${hours}h ${minutes}m ${seconds}s`
        }
    }, 1000)

</script>

<div class="w-100">
<!--    <span>{hours}</span>-->
<!--    :-->
<!--    <span>{minutes}</span>-->
<!--    :-->
<!--    <span>{seconds}</span>-->
    <span>
        {dateDiff}
    </span>
</div>
