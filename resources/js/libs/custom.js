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
// const pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
//     encrypted: true,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER
// });
//
// // Subscribe to the channel we specified in our Laravel Event
// var channel = pusher.subscribe('notification-create');
//
// // Bind a function to a Event (the full Laravel class)
// channel.bind('notification-create', function (data) {
//     console.log(data);
//     alert(JSON.stringify(data));
//     // this is called when the event notification is received...
// });

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

var notificationsWrapper = $('.dropdownNotification');
var notificationsToggle = notificationsWrapper.find('a[data-toggle]');
var notificationsCountElem = notificationsToggle.find('i[data-count]');
var notificationsCount = parseInt(notificationsCountElem.data('count'));
var notifications = notificationsWrapper.find('ul.dropdown-menu');

function UpdateNotifications($input) {
    UpdateNotificationsSymbol();
    UpdateNotification();
    UpdateRoute($input);
}

function UpdateNotificationsSymbol() {
    if (notificationsCount > 0) {
        var NotificationSymbolRed = '<div class ="inline-flex relative -top-2 right-3 w-3 h-3 bg-red-500 rounded-full border-2 border-white dark:border-gray-900">';
        notificationSymbol.html(NotificationSymbolRed);
    }
}

function UpdateNotification() {
    var Notification = '' +
        '<div class="flex py-3 px-4 hover:bg-gray-100 dark:hover:bg-gray-700" >' +
        '<div class="pl-3 w-full" >' +
        '<div class="text-gray-500 text-sm mb-1.5 dark:text-gray-400">' +
        '<span class="font-semibold text-gray-900 dark:text-white" > ' + 'Benutzer' + '</span>:' +
        'Text' +
        '</div>' +
        '<div class="text-xs text-blue-600 dark:text-blue-500">' +
        '{{\Carbon\Carbon::now()->diffForHumans()}}' +
        '</div>' +
        '</div>' +
        '</div>';
}

function UpdateRoute($input) {
    var routeId = $input['route_id'];
    var quantity = $input['quantity'];
    var route = $('#route-' + routeId)[0];
    var routeProgress = $('#route-progessbar-' + routeId)[0];
    var routeOpen = $('#route-open-' + routeId);
    route.dataset.countOpen = parseInt(route.dataset.countOpen) - quantity;
    var routePercent = route.dataset.countAll > 0 ? (route.dataset.countAll - route.dataset.countOpen) / route.dataset.countAll * 100 : 0;
    routeProgress.ariaValueNow = routePercent;
    routeProgress.style.width = routePercent + "%";
    routeOpen.html('<span>Noch Offen</span><strong>' + route.dataset.countOpen + '</strong>');
}

window.Echo.private(`notification-create.${window.actionID}`)
    .listen('NotificationCreate', (e) => {
        UpdateNotifications(e.input);
    });
