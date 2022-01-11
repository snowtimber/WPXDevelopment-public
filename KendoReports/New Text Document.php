<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kendo UI Snippet</title>

    <link rel="stylesheet" href="http://cdn.kendostatic.com/2014.3.1316/styles/kendo.common.min.css">
    <link rel="stylesheet" href="http://cdn.kendostatic.com/2014.3.1316/styles/kendo.rtl.min.css">
    <link rel="stylesheet" href="http://cdn.kendostatic.com/2014.3.1316/styles/kendo.default.min.css">
    <link rel="stylesheet" href="http://cdn.kendostatic.com/2014.3.1316/styles/kendo.dataviz.min.css">
    <link rel="stylesheet" href="http://cdn.kendostatic.com/2014.3.1316/styles/kendo.dataviz.default.min.css">
    <link rel="stylesheet" href="http://cdn.kendostatic.com/2014.3.1316/styles/kendo.mobile.all.min.css">

    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="http://cdn.kendostatic.com/2014.3.1316/js/kendo.all.min.js"></script>
</head>
<body>
  
<script>
var dataSource = new kendo.data.DataSource({
  data: [
    { name: "Jane Doe", age: 30 },
    { name: "John Doe", age: 33 }
  ],
  aggregate: [
    { field: "age", aggregate: "sum" },
    { field: "age", aggregate: "min" },
    { field: "age", aggregate: "max" }
  ]
});
dataSource.fetch(function(){
  var results = dataSource.aggregates().age;
  console.log(results.sum, results.min, results.max); // displays "63 30 33"
});
</script>
</body>
</html>
