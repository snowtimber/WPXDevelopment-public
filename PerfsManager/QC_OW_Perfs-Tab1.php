<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="styles/kendo.common.min.css" />
    <link rel="stylesheet" href="styles/kendo.default.min.css" />
    <link rel="stylesheet" href="styles/kendo.dataviz.min.css" />
    <link rel="stylesheet" href="styles/kendo.dataviz.default.min.css" />

    <script src="js/jquery.min.js"></script>
    <script src="js/kendo.all.min.js"></script>
</head>
<body>
        <script src="../content/shared/js/products.js"></script>

        <div id="example">
            <div id="grid"></div>

            <script>
                $(document).ready(function() {
                    $("#grid").kendoGrid({
                        dataSource: {
                            data: products,
                            schema: {
                                model: {
                                    fields: {
                                        ProductName: { type: "string" },
                                        UnitPrice: { type: "number" },
                                        UnitsInStock: { type: "number" },
                                        Discontinued: { type: "boolean" }
                                    }
                                }
                            },
                            pageSize: 20
                        },
                        height: 550,
                        scrollable: true,
                        sortable: true,
                        filterable: true,
                        pageable: {
                            input: true,
                            numeric: false
                        },
                        columns: [
                            "ProductName",
                            { field: "UnitPrice", title: "Unit Price", format: "{0:c}", width: "130px" },
                            { field: "UnitsInStock", title: "Units In Stock", width: "130px" },
                            { field: "Discontinued", width: "130px" }
                        ]
                    });
                });
            </script>

</div>


</body>
</html>
