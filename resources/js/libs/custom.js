/* =============================================
       TRIGGER COUNTER UP FUNCTION USING WAYPOINTS
   ================================================ */
const counterElem = document.getElementById("counterUp");
if (counterElem) {
    const counterWaypoint = new Waypoint({
        element: counterElem,
        handler: function () {
            vanillaCounterUp(".counter", 5);
        },
        offset: "75%",
    });
}

/* =============================================
    COUNTER UP FUNCTION
================================================ */

function vanillaCounterUp(counterTarget, counterSpeed) {
    const counters = document.querySelectorAll(counterTarget);
    const speed = counterSpeed;

    counters.forEach((counter) => {
        function updateCount() {
            const target = +counter.getAttribute("data-counter");
            const count = +counter.innerText;
            const inc = target / speed;

            if (count < target) {
                counter.innerText = Math.trunc(count + inc);
                setTimeout(updateCount, 1);
            } else {
                counter.innerText = Math.trunc(target);
            }
        }

        updateCount();
    });
}

// Enable pusher logging - don't include this in production
// Pusher.logToConsole = true;

// Initiate the Pusher JS library
const pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
    encrypted: true,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER
});

// Subscribe to the channel we specified in our Laravel Event
var channel = pusher.subscribe('notification-create');

// Bind a function to a Event (the full Laravel class)
channel.bind('notification-create', function (data) {
    console.log(data);
    alert(JSON.stringify(data));
    // this is called when the event notification is received...
});

// import Echo from 'laravel-echo';
//
// window.Pusher = require('pusher-js');
//
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true,
// });
