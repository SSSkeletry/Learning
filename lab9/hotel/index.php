<!DOCTYPE html>
<html lang="uk">
  <head>
    <meta charset="UTF-8" />
    <title>HTML5 Бронювання кімнат в готелі (JavaScript/PHP/MySQL)</title>

    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="daypilot-all.min.js?v=1"></script>

    <style>
      
      body {
        font-family: Arial, sans-serif;
      }
      #dp {
        width: 800px;
        height: 300px;
        margin: 20px;
      }
        .scheduler_default_rowheader_inner
      {
          border-right: 1px solid #ccc;
      }
      .scheduler_default_rowheader.scheduler_default_rowheadercol2
      {
          background: #fff;
      }
      .scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner
      {
          top: 2px;
          bottom: 2px;
          left: 2px;
          background-color: transparent;
          border-left: 5px solid #1a9d13; 
          border-right: 0px none;
      }
      .status_dirty.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner
      {
          border-left: 5px solid #ea3624; 
      }
      .status_cleanup.scheduler_default_rowheadercol2 .scheduler_default_rowheader_inner
      {
          border-left: 5px solid #f9ba25; 
      }
    </style>
  </head>
  <body>
    <header>
      <div class="bg-help">
        <div class="inBox">
          <h1 id="logo">HTML5 Бронювання кімнат в готелі (JavaScript/PHP)</h1>
          <p id="claim">
            AJAX'овий Календар-застосунок з JavaScript/HTML5/jQuery
          </p>
        </div>
      </div>
    </header>
<div>
  <label for="filter">Фільтр кімнат:</label>
  <select id="filter">
    <option value="0">Всі</option>
    <option value="1">Одномісні</option>
    <option value="2">Двомісні</option>
    <option value="4">Сімейні</option>
</select>

</div>
<button id="addRoomBtn">Додати кімнату</button>
    <main>
      <div style="float: left">
        <div id="dp"></div>
      </div>
    </main>

    <div style="clear: both"></div>

    <footer>
      <address>
        (с)Автор лабораторної роботи: студент спеціальності ІПЗ, Соколов Дмитро
        Віталійович
      </address>
    </footer>

<script>
  var dp = new DayPilot.Scheduler("dp");

  dp.startDate = DayPilot.Date.today().firstDayOfMonth();
  dp.days = DayPilot.Date.today().daysInMonth();
  dp.scale = "Day";
  dp.rowHeaderColumns = [
    { title: "Кімната", width: 80 },
    { title: "Місць", width: 80 },
    { title: "Статус", width: 80 }
  ];
  dp.timeHeaders = [
    { groupBy: "Month", format: "MMMM yyyy" },
    { groupBy: "Day", format: "d" }
  ];
$("#addRoomBtn").click(function() {
    var modal = new DayPilot.Modal();
    modal.closed = function() {
      var data = this.result;
      if (data && data.result === "OK") {
        loadResources();
      }
    };
    modal.showUrl("room_new.php");
  });
  dp.onBeforeResHeaderRender = function(args) {
    var beds = function(count) {
      return count + " ліж" + (count > 1 ? "ка" : "ко");
    };

    args.resource.columns[1].html = beds(args.resource.capacity);
    args.resource.columns[2].html = args.resource.status;

    switch (args.resource.status) {
      case "Брудна":
        args.resource.cssClass = "status_dirty";
        break;
      case "Прибирається":
        args.resource.cssClass = "status_cleanup";
        break;
    }
  };
dp.onTimeRangeSelected = function (args) {

  var modal = new DayPilot.Modal();
  modal.closed = function() {
      dp.clearSelection();
      
      var data = this.result;
      if (data && data.result === "OK") {
          loadEvents();
      }
  };
  modal.showUrl("new.php?start=" + args.start + "&end=" + args.end + "&resource=" + args.resource);
  
};
  dp.onEventClick = function(args) {
    var modal = new DayPilot.Modal();
    modal.closed = function() {

        var data = this.result;
        if (data && data.result === "OK") {
            loadEvents();
        }
    };
  modal.showUrl("edit.php?id=" + args.e.id());
};

  dp.onEventMoved = function (args) {
    $.post("back_move.php", 
    {
        id: args.e.id(),
        newStart: args.newStart.toString(),
        newEnd: args.newEnd.toString(),
        newResource: args.newResource
    }, 
    function(data) {
        dp.message(data.message);
    });
  };

  dp.eventDeleteHandling = "Update";

dp.onEventDeleted = function(args) {
  $.post("back_delete.php", 
  {
      id: args.e.id()
  }, 
  function() {
      dp.message("Deleted.");
  });
};

dp.onBeforeEventRender = function(args) {
  var start = new DayPilot.Date(args.e.start);
  var end = new DayPilot.Date(args.e.end);

  var today = DayPilot.Date.today();
  var now = new DayPilot.Date();

  args.e.html = args.e.text + " (" + start.toString("M/d/yyyy") + " - " + end.toString("M/d/yyyy") + ")";

  switch (args.e.status) {
      case "new":
          var in2days = today.addDays(1);

          if (start < in2days) {
              args.e.barColor = 'red';
              args.e.toolTip = 'Застаріле (не підтверджено вчасно)';
          }
          else {
              args.e.barColor = 'orange';
              args.e.toolTip = 'Новий';
          }
          break;
      case "confirmed":
          var arrivalDeadline = today.addHours(18);

          if (start < today || (start.getDatePart() === today.getDatePart() && now > arrivalDeadline)) { 
              args.e.barColor = "#f41616";  
              args.e.toolTip = 'Пізнє прибуття';
          }
          else {
              args.e.barColor = "green";
              args.e.toolTip = "Підтверджено";
          }
          break;
      case 'arrived':
          var checkoutDeadline = today.addHours(10);

          if (end < today || (end.getDatePart() === today.getDatePart() && now > checkoutDeadline)) { 
              args.e.barColor = "#f41616";  
              args.e.toolTip = "Пізній виїзд";
          }
          else
          {
              args.e.barColor = "#1691f4";  
              args.e.toolTip = "Прибув";
          }
          break;
      case 'checkedout': 
          args.e.barColor = "gray";
          args.e.toolTip = "Перевірено";
          break;
      default:
          args.e.toolTip = "Невизначений стан";
          break;
  }

  args.e.html = args.e.html + "<br /><span style='color:gray'>" + args.e.toolTip + "</span>";
  
  var paid = args.e.paid;
  var paidColor = "#aaaaaa";

  args.e.areas = [
      { bottom: 10, right: 4, html: "<div style='color:" + paidColor + "; font-size: 8pt;'>Paid: " + paid + "%</div>", v: "Visible"},
      { left: 4, bottom: 8, right: 4, height: 2, html: "<div style='background-color:" + paidColor + "; height: 100%; width:" + paid + "%'></div>", v: "Visible" }
  ];

};
  function loadResources() {
    $.post("back_rooms.php",{ capacity: $("#filter").val() }, function(data) {
      dp.resources = data;
      dp.update();
    }).fail(function(jqXHR, textStatus, errorThrown) {
      console.error("Помилка завантаження кімнат:", textStatus, errorThrown);
    });
    
  }

  function loadEvents() {
    var start = dp.visibleStart().toString("yyyy-MM-dd");
    var end = dp.visibleEnd().toString("yyyy-MM-dd");

    $.post("back_events.php", {
      start: start,
      end: end
    }, function(data) {
      dp.events.list = data;
      dp.update();
    }).fail(function(jqXHR, textStatus, errorThrown) {
      console.error("Помилка завантаження подій:", textStatus, errorThrown);
      console.error("Вміст відповіді:", jqXHR.responseText);
    });
  }
  
    $(document).ready(function() {
    $("#filter").change(function() {
        loadResources();
    });
  });
  dp.allowEventOverlap = false
  dp.init();
  loadResources();
  loadEvents();
</script>

  </body>
</html>
