<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Laravel</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css"/>
  <!-- Styles -->
  <style>
    html, body {
      background-color: #fff;
      color: #636b6f;
      font-family: 'Raleway', sans-serif;
      font-weight: 500;
      height: 100vh;
      margin: 0;
    }

    .full-height {
      height: 100vh;
    }

    .flex-center {
      align-items: center;
      display: flex;
      justify-content: center;
    }

    .position-ref {
      position: relative;
    }

    .top-right {
      position: absolute;
      right: 10px;
      top: 18px;
    }

    .content {
      text-align: center;
    }

    .title {
      font-size: 84px;
    }

    .links > a {
      color: #636b6f;
      padding: 0 25px;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: .1rem;
      text-decoration: none;
      text-transform: uppercase;
    }

    .m-b-md {
      margin-bottom: 30px;
    }

    [v-cloak] {
      display: none;
    }

    li {
      list-style-type: none;
      background: rgba(238, 238, 238, 0.35);
      padding: 20px;
    }
  </style>
</head>
<body>
<div id="app" v-cloak>
  <h1 class="content">Tasks</h1>
  <div class="row">
    <h2 class="content">Notification List</h2>
    <ul>
      <li v-if="tasks.length" v-for="task in tasks">
        <h3>@{{ task.title }}</h3>
        <h5>@{{ task.description }}</h5>
        <hr>
      </li>
      <li v-else>No Notifications</li>
    </ul>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.2.6/vue.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.3/socket.io.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.16.1/axios.min.js"></script>
<script>
  // request permission on page load
  document.addEventListener('DOMContentLoaded', function() {
    if (!Notification) {
      alert('Desktop notifications not available in your browser. Try Chromium.');
      return;
    }

    if (Notification.permission !== "granted")
      Notification.requestPermission();
  });
</script>
<script>
  var socket = io('http://127.0.0.1:3000');
  new Vue({
    el: '#app',

    data: {
      tasks: [],
      user: {},
    },

    methods: {
      notifyMe: function(data) {
        if (Notification.permission !== "granted")
          Notification.requestPermission();
        else {
          var notification = new Notification(data.task.title, {
            icon: '/images/agentcis_invert.png',
            body: data.task.description,
          });

          notification.onclick = function() {
            window.open('/tasks/' + data.task.id);
          };
        }

      }
    },
    mounted: function() {
      socket.on('testChannel:App\\Events\\TaskCreated', function(data) {
        this.tasks.unshift(data.task);
        this.user = data.user;

        this.notifyMe(data);
        toastr.success('', data.user.name + ' created a new Task, ' + data.task.title)
      }.bind(this));

      axios.get('tasks/get-all')
              .then(function(response) {
                this.tasks = response.data;
              }.bind(this));
    }
  });
</script>
</body>
</html>
