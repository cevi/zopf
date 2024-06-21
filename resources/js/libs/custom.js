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

function UpdateNotifications(data) {
    UpdateNotificationsSymbol();
    UpdateNotification(data['input']);
    UpdateRoute(data['input']);
    if (typeof UpdateIconArray === "function") {
        UpdateIconArray(data['action']);
    }
    if (typeof UpdateGraph === "function") {
        UpdateGraph();
    }
    if (typeof UpdateList === "function") {
        UpdateList(data['input']);
    }
}
window.UpdateNotifications = UpdateNotifications;

function UpdateNotificationsSymbol() {
    $('#notificationSymbol').html('<div class ="inline-flex relative -top-2 right-3 w-3 h-3 bg-red-500 rounded-full border-2 border-white dark:border-gray-900">');
}
window.UpdateNotificationsSymbol = UpdateNotificationsSymbol;

function UpdateNotification(input) {
    var notificationsWrapper = $('#dropdownNotifications');
    var notificationsList = notificationsWrapper.find('#notificationList');
    var today = new Date();
    var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
    var Notification = '' +
        '<div class="flex py-3 px-4 hover:bg-gray-100 dark:hover:bg-gray-700" >' +
        '<div class="pl-3 w-full" >' +
        '<div class="text-gray-500 text-sm mb-1.5 dark:text-gray-400">' +
        '<span class="font-semibold text-gray-900 dark:text-white" > ' + input['user'] + '</span>: ' +
        input['text'] +
        '</div>' +
        '<div class="text-xs text-blue-600 dark:text-blue-500">' +
        time +
        '</div>' +
        '</div>' +
        '</div>';
    notificationsList.html(Notification + notificationsList.html());
}
window.UpdateNotification = UpdateNotification;

function UpdateRoute($input) {
    if ($input['route_id'] !== undefined) {
        var routeId = $input['route_id'];
        var quantity = $input['quantity'];
        var route = $('#route-' + routeId)[0];
        if (route !== undefined) {
            var routeProgress = $('#route-progessbar-' + routeId)[0];
            var routeOpen = $('#route-open-' + routeId);
            route.dataset.countOpen = parseInt(route.dataset.countOpen) - quantity;
            var routePercent = route.dataset.countAll > 0 ? (route.dataset.countAll - route.dataset.countOpen) / route.dataset.countAll * 100 : 0;
            routeProgress.ariaValueNow = routePercent;
            routeProgress.style.width = routePercent + "%";
            routeOpen.html('<span>Noch Offen</span><strong>' + route.dataset.countOpen + '</strong>');
            if (route.dataset.countOpen <= 0) {
                route.remove();
            }
        }
    }
}
window.UpdateRoute = UpdateRoute;

var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
// Change the icons inside the button based on previous settings

if (localStorage.getItem('color-theme') === 'dark' ||
    (!('color-theme' in localStorage) &&
        window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    themeToggleLightIcon.classList.remove('hidden');
    document.documentElement.classList.add('dark');
    localStorage.setItem('color-theme', 'dark');
} else {
    themeToggleDarkIcon.classList.remove('hidden');
}

var themeToggleBtn = document.getElementById('theme-toggle');

themeToggleBtn.addEventListener('click', function () {

    // toggle icons inside button
    themeToggleDarkIcon.classList.toggle('hidden');
    themeToggleLightIcon.classList.toggle('hidden');

    // if set via local storage previously
    if (localStorage.getItem('color-theme')) {
        if (localStorage.getItem('color-theme') === 'light') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        }

        // if NOT set via local storage previously
    } else {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        }
    }

});
