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
  function loadResources() {
    $.post("back_rooms.php", function(data) {
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

  dp.init();
  loadResources();
  loadEvents();
</script>

  </body>
</html>
