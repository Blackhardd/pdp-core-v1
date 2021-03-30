<tr>
	<td>
		<table width="100%" style="border-spacing: 0">
			<tr>
				<td><h4><?=__( 'Контактные данные', 'pdp_core' ); ?>:</h4></td>
			</tr>
			<tr>
				<td><?=$data['name']; ?></td>
			</tr>
			<?=( $data['email'] ) ? "<tr><td><a href='mailto:{$data['email']}'>{$data['email']}</a></td></tr>" : ''; ?>
			<?=( $data['phone'] ) ? "<tr><td><a href='tel:{$data['phone']}'>{$data['phone']}</a></td></tr>" : ''; ?>
		</table>
	</td>
</tr>
<tr>
	<td>
		<table width="100%" style="border-spacing: 0">
			<tr>
				<td><?=__( 'Сообщение', 'pdp_core' ); ?>:</td>
			</tr>
			<tr>
				<td><?=$data['message']; ?></td>
			</tr>
		</table>
	</td>
</tr>