<?php

/**
 * @var array $data
 */

$form = (new CForm('post', 'zabbix.php'))
    ->addVar('action', 'messageoftheday.save')
    ->setName('messageoftheday_form');

// ---- Flash notices ----
if (array_key_exists('saved', $_GET)) {
    if ($_GET['saved'] === '1') {
        $form->addItem(
            (new CDiv(_('Configuration saved successfully.')))
                ->addClass(ZBX_STYLE_MSG_GOOD)
                ->addStyle('margin-bottom: 8px; padding: 8px 12px;')
        );
    } else {
        $form->addItem(
            (new CDiv(_('Failed to save configuration. Check that config.json is writable by the web server.')))
                ->addClass(ZBX_STYLE_MSG_BAD)
                ->addStyle('margin-bottom: 8px; padding: 8px 12px;')
        );
    }
}

if (!$data['config_writable']) {
    $form->addItem(
        (new CDiv(_('Warning: config.json is not writable by the web server. Changes cannot be saved.')))
            ->addClass(ZBX_STYLE_RED)
            ->addStyle('margin-bottom: 8px;')
    );
}

$form_list = (new CFormList())
    ->addRow(
        (new CLabel(_('Enabled'), 'enabled')),
        (new CCheckBox('enabled', 1))
            ->setChecked($data['enabled'])
    )
    ->addRow(
        (new CLabel(_('Message'), 'message'))->setAsteriskMark(),
        (new CTextArea('message', $data['message']))
            ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
            ->setAttribute('rows', 4)
            ->setAttribute('maxlength', 2048)
    )
    ->addRow(
        (new CLabel(_('Banner type'), 'type')),
        (new CSelect('type'))
            ->setFocusableElementId('type')
            ->addOptions(CSelect::createOptionsFromArray([
                'info'    => _('Information (blue)'),
                'warning' => _('Warning (yellow)'),
                'danger'  => _('Critical (red)'),
                'success' => _('Success (green)')
            ]))
            ->setValue($data['type'])
    )
    ->addRow(
        (new CLabel(_('Dismissible'), 'dismissible')),
        (new CCheckBox('dismissible', 1))
            ->setChecked($data['dismissible'])
    )
    ->addRow(
        (new CLabel(_('Show to'), 'show_to')),
        (new CSelect('show_to'))
            ->setFocusableElementId('show_to')
            ->addOptions(CSelect::createOptionsFromArray([
                'all'    => _('All users'),
                'admins' => _('Administrators only')
            ]))
            ->setValue($data['show_to'])
    )
    // ---- Link section ----
    ->addRow(
        (new CLabel(_('Link URL'), 'link_url')),
        (new CTextBox('link_url', $data['link_url']))
            ->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
            ->setAttribute('placeholder', 'https://example.com/more-info')
            ->setAttribute('maxlength', 2048)
    )
    ->addRow(
        (new CLabel(_('Link label'), 'link_label')),
        (new CTextBox('link_label', $data['link_label']))
            ->setWidth(ZBX_TEXTAREA_MEDIUM_WIDTH)
            ->setAttribute('placeholder', 'Read more')
            ->setAttribute('maxlength', 100)
    );

$form->addItem(
    (new CTabView())
        ->addTab('motd_tab', _('Banner settings'), $form_list)
);

$form->addItem(
    (new CFormActions())
        ->addItem(new CSubmit('update', _('Update')))
);

(new CHtmlPage())
    ->setTitle(_('Message of the Day'))
    ->setDocUrl('')
    ->addItem($form)
    ->show();
