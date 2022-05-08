<!-- EXTERNAL CSS -->



<link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css' rel='stylesheet'>
<link href='https://cdn.datatables.net/plug-ins/f2c75b7247b/integration/bootstrap/3/dataTables.bootstrap.css' rel='stylesheet'>
<link href='https://cdn.datatables.net/responsive/1.0.4/css/dataTables.responsive.css' rel='stylesheet'>

<style>
    body { 
        font-size: 140%; 
    }

    h2 {
        text-align: center;
        padding: 20px 0;
    }

    table caption {
        padding: .5em 0;
    }

    table.dataTable th,
    table.dataTable td {
        white-space: nowrap;
    }

    .p {
        text-align: center;
        padding-top: 140px;
        font-size: 14px;
    }
</style>

<h2>Juragan 99 List Pesanan</h2>

<div class="container">
  <div class="row">
    <div class="col-xs-12">
      <table summary="This table shows how to create responsive tables using Datatables' extended functionality" class="table table-bordered table-hover dt-responsive">
        <caption class="text-center">Menu Makanan</a>:</caption>
        <thead>
          <tr>
            <th>Nama</th>
            <th>Ticket</th>
            <th>Seat</th>
            <th>Menu</th>
          </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $key => $value) { ?>
                <tr>
                    <td><?= $value->name ?></td>
                    <td><?= $value->ticket_number ?></td>
                    <td><?= $value->seat_number ?></td>
                    <td><?= $value->food_name ?></td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" class="text-center">PT GILANG SEMBILAN SEMBILAN</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<p class="p">Demo by George Martsoukos. <a href="http://www.sitepoint.com/responsive-data-tables-comprehensive-list-solutions" target="_blank">See article</a>.</p>

<!-- EXTERNAL JAVASCRIPT -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/f2c75b7247b/integration/bootstrap/3/dataTables.bootstrap.js"></script>
<script src="https://cdn.datatables.net/responsive/1.0.4/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script>
    $('table').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'pdf', 'print'
        ]
    });
</script>