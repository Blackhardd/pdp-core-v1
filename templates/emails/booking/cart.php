<tr>
	<td>
		<table width="100%" style="border-spacing: 0">
			<tr>
				<td colspan="2"><h4><?=__( 'Услуги', 'pdp_core' ); ?></h4></td>
			</tr>
			<?php foreach( $data['cart']->items as $service ) :
				$price = 0;

				if( count( $service->prices ) > 1 && $service->master ){
					$price = $service->prices[$data['cart']->hair_length][$data['cart']->master_option];
				}
				else if( count( $service->prices ) > 1 && !$service->master ){
					$price = $service->prices[$data['cart']->hair_length][0];
				}
				else if( count( $service->prices ) == 1 && $service->master ){
					$price = $service->prices[0][$data['cart']->master_option];
				}
				else{
					$price = $service->prices[0][0];
				} ?>
				<tr>
					<td><?=$service->name->ru; ?></td>
					<td><?=$price; ?> грн</td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="2"><?=__( 'Итого', 'pdp_core' ); ?> <?=$data['total']; ?> грн</td>
			</tr>
		</table>
	</td>
</tr>