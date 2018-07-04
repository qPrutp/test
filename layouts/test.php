<form method="post">
    <div class="url">
        <label style="font-weight: bolder">введіть адрес сайту <input type="text" id="url" name="url" placeholder="http://www.example.com/"></label>
        <button class="button" id="button" disabled>Перевірити</button>
    </div>
</form>


<?php if($result != []) { ?>
    <h4 class="info-block">Ресурс що перевіряється:</h4>
    <h3 class="info-block"><?php $site_name = $_POST['url']; echo $site_name; ?></h3>

    <div class="div-table">
        <button class="button" onclick="location.href='../test-sipius-new.xlsx'">Завантажити дані</button>
		<table class="table-test">
			<tr class="table-header">
				<th class="table-number table-text-center">№</th>
				<th class="table-name-test table-text-left">Название проверки</th>
				<th class="table-status table-text-center">Статус</th>
				<th class="table-empty-header table-text-left"></th>
				<th class="table-current-state table-text-left">Текущее состояние</th>
			</tr>
			<?php foreach ($result as $k => $v) { ?>
			<tr class="table-content-gaps"><th colspan="5">&nbsp;</th></tr>
			<tr class="table-content">
                <td class="table-number table-text-center" rowspan="2">		<?php echo $k; ?>                      </td>
                <td class="table-name-test table-text-left" rowspan="2">	<?php echo $v['test']; ?>              </td>
                <td class="table-status table-text-center"
                	<?php if($v['status'] == 'Оk')
                		echo 'style="background: green;"';
                	else
                		echo 'style="background: crimson;"'; ?>
                										rowspan="2">		<?php echo $v['status']; ?>				</td>
                <td class="table-empty-header table-text-left">				<?php echo STATE_TITLE; ?>				</td>
                <td class="table-current-state table-text-left">			<?php echo $v['state']; ?>				</td>
            </tr>
            <tr class="table-content">
            	<td class="table-empty-header table-text-left">				<?php echo RCOMENDATION_TITLE; ?>		</td>
                <td class="table-current-state table-text-left">			<?php echo $v['recommendation']; } ?>	</td>
            </tr>
		</table>
    </div>
<?php } ?>
