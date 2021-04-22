<?php if( isset( $data['page_title'] ) && isset( $data['page_url'] ) ) : ?>
    <tr>
        <td>
            <table width="100%" style="margin-bottom: 30px; border-spacing: 0">
                <tr>
                    <td><h4 style="margin: 0;"><?=__( 'Страница', 'pdp_core' ); ?>:</h4></td>
                    <td><a href="<?=$data['page_url']; ?>"><?=$data['page_title']; ?></a></td>
                </tr>
            </table>
        </td>
    </tr>
<?php endif; ?>
<?php if( isset( $data['salon_name'] ) ) : ?>
    <tr>
        <td>
            <table width="100%" style="margin-bottom: 30px; border-spacing: 0">
                <tr>
                    <td><h4 style="margin: 0;"><?=__( 'Салон', 'pdp_core' ); ?>:</h4></td>
                    <td><?=$data['salon_name']; ?></td>
                </tr>
            </table>
        </td>
    </tr>
<?php endif; ?>
<tr>
	<td>
		<table width="100%" style="margin-bottom: 30px; border-spacing: 0">
			<tr>
				<td><h4><?=__( 'Контактные данные', 'pdp_core' ); ?>:</h4></td>
			</tr>
			<tr>
				<td><?=$data['data']['name']; ?></td>
			</tr>
			<?=( isset( $data['data']['email'] ) ) ? "<tr><td><a href='mailto:{$data['data']['email']}'>{$data['data']['email']}</a></td></tr>" : ''; ?>
			<?=( $data['data']['phone'] ) ? "<tr><td><a href='tel:{$data['data']['phone']}'>{$data['data']['phone']}</a></td></tr>" : ''; ?>
		</table>
	</td>
</tr>
<?php if( isset( $data['data']['cart']->master_option ) || isset( $data['data']['is_hair_services'] ) ) :
	$hair_length = pdp_get_hair_length_title( $data['data']['cart']->hair_length ); ?>
    <tr>
        <td>
            <table width="100%" style="margin-bottom: 30px; border-spacing: 0">
                <tr>
                    <td><h4><?=__( 'Дополнительная информация', 'pdp_core' ); ?>:</h4></td>
                </tr>
                <?=( $data['data']['cart']->master_option ) ? "<tr><td>Старший мастер.</td></tr>" : ''; ?>
                <?=( $data['data']['is_hair_services'] ) ? "<tr><td>Длина волос {$hair_length}.</td></tr>" : ''; ?>
            </table>
        </td>
    </tr>
<?php endif; ?>