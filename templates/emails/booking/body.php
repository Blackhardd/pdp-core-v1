<?php if( isset( $data['salon_name'] ) ) : ?>
    <tr>
        <td>
            <table width="100%" style="border-spacing: 0">
                <tr>
                    <td><h4><?=__( 'Салон', 'pdp_core' ); ?>:</h4></td>
                    <td><?=$data['salon_name']; ?></td>
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
				<td><?=$data['data']['name']; ?></td>
			</tr>
			<?=( $data['data']['email'] ) ? "<tr><td><a href='mailto:{$data['data']['email']}'>{$data['data']['email']}</a></td></tr>" : ''; ?>
			<?=( $data['data']['phone'] ) ? "<tr><td><a href='tel:{$data['data']['phone']}'>{$data['data']['phone']}</a></td></tr>" : ''; ?>
		</table>
	</td>
</tr>
<?php if( isset( $data['data']['cart']->master_option ) || isset( $data['data']['is_hair_services'] ) ) :
	$hair_length = pdp_get_hair_length_title( $data['data']['cart']->hair_length ); ?>
    <tr>
        <td>
            <table width="100%" style="border-spacing: 0">
                <tr>
                    <td><h4><?=__( 'Дополнительная информация', 'pdp_core' ); ?>:</h4></td>
                </tr>
                <?=( $data['data']['cart']->master_option ) ? "<tr><td>Старший мастер.</td></tr>" : ''; ?>
                <?=( $data['data']['is_hair_services'] == 'true' ) ? "<tr><td>Длина волос {$hair_length}.</td></tr>" : ''; ?>
            </table>
        </td>
    </tr>
<?php endif; ?>