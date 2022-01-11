<!DOCTYPE html>
<html>
<head>
    <title>Filter menu customization</title>
    <meta charset="utf-8">
    <link href="/WPXDevelopment\KendoReports/Kendo UI Professional Q3 2014/examples/content/shared/styles/examples-offline.css" rel="stylesheet">
    <link href="/WPXDevelopment/KendoReports/Kendo UI Professional Q3 2014/styles/kendo.common.min.css" rel="stylesheet">
    <link href="/WPXDevelopment/KendoReports/Kendo UI Professional Q3 2014/styles/kendo.rtl.min.css" rel="stylesheet">
    <link href="/WPXDevelopment/KendoReports/Kendo UI Professional Q3 2014/styles/kendo.default.min.css" rel="stylesheet">
    <link href="/WPXDevelopment/KendoReports/Kendo UI Professional Q3 2014/styles/kendo.dataviz.min.css" rel="stylesheet">
    <link href="/WPXDevelopment/KendoReports/Kendo UI Professional Q3 2014/styles/kendo.dataviz.default.min.css" rel="stylesheet">
    <script src="/WPXDevelopment/KendoReports/Kendo UI Professional Q3 2014/js/jquery.min.js"></script>
    <script src="/WPXDevelopment/KendoReports/Kendo UI Professional Q3 2014/js/angular.min.js"></script>
    <script src="/WPXDevelopment/KendoReports/Kendo UI Professional Q3 2014/js/kendo.all.min.js"></script>
    <script src="/WPXDevelopment\KendoReports/Kendo UI Professional Q3 2014/examples/content/shared/js/console.js"></script>
	<script src="/WPXDevelopment/KendoReports/Kendo UI Professional Q3 2014/js/jszip.min.js"></script>
    <script>
        
    </script>
    
    
</head>
<body>
    
        <!--<a class="offline-button" href="../index.html">Back</a>-->
    
            <script src="/WPXDevelopment\KendoReports/Kendo UI Professional Q3 2014/examples/content/shared/js/people.js"></script>

        <div id="example">
            <div id="grid"></div>

            <script>
                $(document).ready(function() {
                    $("#grid").kendoGrid({
						toolbar: ["excel"],
						excel: {
							fileName: "Kendo UI Grid Export.xlsx",
							proxyURL: "http://demos.telerik.com/kendo-ui/service/export",
							filterable: true
						},
                        dataSource: {
							data: [
								{ FirstName: "John", LastName: "Doe", Name: "Jane Doe", City: "Denver", Title: "CEO", BirthDate: "2014/01/01" },
								{ FirstName: "Jane", LastName: "Doe", Name: "John Doe", City: "Denver", Title: "CEO", BirthDate: "2014/01/01" }
							  ],
                            //data: createRandomData(100),
                            schema: {
                                model: {
                                    fields: {
                                        City: { type: "string" },
                                        Title: { type: "string" },
                                        BirthDate: { type: "date" }
                                    }
                                }
                            },
                            pageSize: 10000
                        },
                        height: 550,
                        scrollable: true,
                        filterable: {
                            extra: false,
                            operators: {
                                string: {
                                    startswith: "Starts with",
                                    eq: "Is equal to",
                                    neq: "Is not equal to"
                                }
                            }
                        },
                        pageable: true,
                        columns: [
                            {
                                title: "Name",
                                width: 160,
                                filterable: false,
                                template: "#=FirstName# #=LastName#"
                            },
                            {
                                field: "City",
                                width: 130,
                                filterable: {
                                    ui: cityFilter
                               }
                            },
                            {
                                field: "Title",
                                filterable: {
                                    ui: titleFilter
                                }
                            },
                            {
                                field: "BirthDate",
                                title: "Birth Date",
                                format: "{0:MM/dd/yyyy HH:mm tt}",
                                filterable: {
                                    ui: "datetimepicker"
                                }
                            }
                       ]
                    });
                });

                function titleFilter(element) {
                    element.kendoAutoComplete({
                        dataSource: titles
                    });
                }

                function cityFilter(element) {
                    element.kendoDropDownList({
                        dataSource: cities,
                        optionLabel: "--Select Value--"
                    });
                }

            </script>
        </div>


    
    
</body>
</html>
