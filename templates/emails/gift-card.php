<tr>
	<td>
		<table width="100%" style="border-spacing: 0">
			<tr>
				<td><h4><?=__( 'Контактные данные', 'pdp_core' ); ?>:</h4></td>
			</tr>
			<tr>
				<td><?=$data['data']['name']; ?></td>
			</tr>
			<?=( $data['data']['email'] ) ? "<tr><td><a href='mailto:{$data['data']['email']}'>{$data['data']['email']}</a></td></tr>" : ''; ?>
			<?=( $data['data']['phone'] ) ? "<tr><td><a href='tel:{$data['data']['phone']}'>{$data['data']['phone']}</a></td></tr>" : ''; ?>
		</table>
	</td>
</tr>
<tr>
	<td>
		<table width="100%" style="border-spacing: 0">
			<tr>
				<td><?=__( 'Заказ', 'pdp_core' ); ?>:</td>
			</tr>
			<tr>
				<td><?=sprintf( __( 'Подарочный сертификат на сумму %s грн.', 'pdp_core' ), $data['data']['card'] ); ?></td>
			</tr>
		</table>
	</td>
</tr>