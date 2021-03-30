<tr>
	<td>
		<table width="100%" style="border-spacing: 0">
			<tr>
				<td colspan="2"><?=__( 'Услуги', 'pdp_core' ); ?></td>
			</tr>
			<?php foreach( $cart->items as $service ) :
				$price = 0;

				if( count( $service->prices ) > 1 && $service->master ){
					$price = $service->prices[$cart->hair_length][$cart->master_option];
				}
				else if( count( $service->prices ) > 1 && !$service->master ){
					$price = $service->prices[$cart->hair_length][0];
				}
				else if( count( $service->prices ) == 1 && $service->master ){
					$price = $service->prices[0][$cart->master_option];
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
				<td colspan="2"><?=__( 'Итого', 'pdp_core' ); ?> <?=$cart->total; ?> грн</td>
			</tr>
		</table>
	</td>
</tr>