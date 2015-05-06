<table class="table table-condensed table-responsive table-hover" style="font-size:60%">
    <thead>
        <tr>
            <th style="width:6%">Name</th>
            <th style="width:6%">4 hr</th>
            <th style="width:6%">8 hr</th>
            <th style="width:6%">1 day</th>
            <th style="width:6%">2 day</th>
            <th style="width:6%">3 day</th>
            <th style="width:6%">4 day</th>
            <th style="width:6%">5 day</th>
            <th style="width:6%">6 day</th>
            <th style="width:6%">Week</th>
            <th style="width:6%">Weekend</th>
            <th style="width:6%">Subsequent Days</th>
            <th style="width:6%">Days per week</th>
            <th style="width:6%">Thereafter</th>
            <th style="width:6%">Min days</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($items as $i): ?>
            <tr>
                <td><?php echo $i->name; ?></td>
                <td><?php echo $i->_4hr == "" ? "":$i->_4hr.'%'; ?></td>
                <td><?php echo $i->_8hr == "" ? "":$i->_8hr.'%'; ?></td> 
                <td><?php echo $i->_1day == "" ? "":$i->_1day.'%'; ?></td>
                <td><?php echo $i->_2day == "" ? "":$i->_2day.'%'; ?></td>
                <td><?php echo $i->_3day == "" ? "":$i->_3day.'%'; ?></td>
                <td><?php echo $i->_4day == "" ? "":$i->_4day.'%'; ?></td>
                <td><?php echo $i->_5day == "" ? "":$i->_5day.'%'; ?></td>
                <td><?php echo $i->_6day == "" ? "":$i->_6day.'%'; ?></td>
                <td><?php echo $i->week == "" ? "":$i->week.'%'; ?></td>
                <td><?php echo $i->weekend == "" ? "":$i->weekend.'%'; ?></td>
                <td><?php echo $i->subsequent_days == "" ? "":$i->subsequent_days.'%'; ?></td>
                <td><?php echo $i->days_per_week == "" ? "":$i->days_per_week; ?></td>
                <td><?php echo $i->thereafter == "" ? "":$i->thereafter; ?></td>
                <td><?php echo $i->min_days == "" ? "":$i->min_days; ?></td>
            </tr>
        <?php endforeach;  ?>
    </tbody>
</table>
