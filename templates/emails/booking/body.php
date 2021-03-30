<?php if( isset( $salon_name ) ) : ?>
    <tr>
        <td>
            <table width="100%" style="border-spacing: 0">
                <tr>
                    <td><h4><?=__( 'Салон', 'pdp_core' ); ?>:</h4></td>
                    <td><?=$salon_name; ?></td>
                </tr>
            </table>
        </td>
    </tr>
<?php endif; ?>
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
<?php if( isset( $data['cart']->master_option ) || isset( $data['is_hair_services'] ) ) : ?>
    <tr>
        <td>
            <table width="100%" style="border-spacing: 0">
                <tr>
                    <td><h4><?=__( 'Дополнительная информация', 'pdp_core' ); ?>:</h4></td>
                </tr>
                <?=( $data['cart']->master_option ) ? "<tr><td>Старший мастер.</td></tr>" : ''; ?>
                <?=( $data['is_hair_services'] ) ? "<tr><td>Длина волос {$this->hair_lengths[$data['cart']->hair_length]}.</td></tr>" : ''; ?>
            </table>
        </td>
    </tr>
<?php endif; ?>