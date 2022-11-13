<script>
    import route from 'ziggy-js';

    import WhereByEmbed from "../Components/WhereBy/WhereByEmbed.svelte";
    import Countdown from "./Timers/Countdown.svelte";

    export let liveLesson = {}
    export let isAdmin = false

    const roomUrl = isAdmin ? liveLesson.host_room_url : liveLesson.room_url

    console.log('Live Lesson: ', {liveLesson, roomUrl})
</script>

<div class="row mb-3 p-4">
    <div class="text-center" style="font-weight: bold;">
        <h2>{liveLesson.title}</h2>
    </div>
    <div class="text-center">
        Inizia tra: <Countdown countDownDate={liveLesson.start_date} />
    </div>
</div>
<div class="row w-100">
    <WhereByEmbed title={liveLesson.title} roomUrl={roomUrl} />
</div>

<style>
    /*.main-section {*/
    /*    font-size: 2.5em;*/
    /*    background-color: white;*/
    /*    cursor: pointer;*/
    /*    padding: 5%;*/
    /*}*/
</style>
