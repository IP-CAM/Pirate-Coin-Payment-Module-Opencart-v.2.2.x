<?php


class ControllerPaymentPirate extends Controller {

    private $error = array();

    public function index() {

        $this->load->model('setting/setting');

        $data = $this->load->language('payment/pirate');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('pirate', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ''
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/pirate', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('payment/pirate', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('localisation/currency');
        $data['currencies'] = $this->model_localisation_currency->getCurrencies();

        if (isset($this->request->post['pirate_user'])) {
            $data['pirate_user'] = $this->request->post['pirate_user'];
        } else if ($this->config->get('pirate_user')) {
            $data['pirate_user'] = $this->config->get('pirate_user');
        } else {
            $data['pirate_user'] = 'user';
        }

        if (isset($this->request->post['pirate_password'])) {
            $data['pirate_password'] = $this->request->post['pirate_password'];
        } else if ($this->config->get('electrum_password')) {
            $data['pirate_password'] = $this->config->get('pirate_password');
        } else {
            $data['pirate_password'] = 'changeme';
        }

        if (isset($this->request->post['pirate_host'])) {
            $data['pirate_host'] = $this->request->post['pirate_host'];
        } else if ($this->config->get('pirate_host')) {
            $data['pirate_host'] = $this->config->get('pirate_host');
        } else {
            $data['pirate_host'] = 'localhost';
        }

        if (isset($this->request->post['pirate_port'])) {
            $data['pirate_port'] = $this->request->post['pirate_port'];
        } else if ($this->config->get('pirate_port')) {
            $data['pirate_port'] = $this->config->get('pirate_port');
        } else {
            $data['pirate_port'] = 45453;
        }

        if (isset($this->request->post['pirate_total'])) {
            $data['pirate_total'] = $this->request->post['pirate_total'];
        } else {
            $data['pirate_total'] = $this->config->get('pirate_total');
        }

        if (isset($this->request->post['pirate_qr'])) {
            $data['pirate_qr'] = $this->request->post['pirate_qr'];
        } else {
            $data['pirate_qr'] = $this->config->get('pirate_qr');
        }

        if (isset($this->request->post['pirate_currency'])) {
            $data['pirate_currency'] = $this->request->post['pirate_currency'];
        } else {
            $data['pirate_currency'] = $this->config->get('pirate_currency');
        }

        if (isset($this->request->post['pirate_order_status_id'])) {
            $data['pirate_order_status_id'] = $this->request->post['pirate_order_status_id'];
        } else {
            $data['pirate_order_status_id'] = $this->config->get('pirate_order_status_id');
        }

        if (isset($this->request->post['pirate_geo_zone_id'])) {
            $data['pirate_geo_zone_id'] = $this->request->post['pirate_geo_zone_id'];
        } else {
            $data['pirate_geo_zone_id'] = $this->config->get('pirate_geo_zone_id');
        }

        if (isset($this->request->post['pirate_status'])) {
            $data['pirate_status'] = $this->request->post['pirate_status'];
        } else {
            $data['pirate_status'] = $this->config->get('pirate_status');
        }

        if (isset($this->request->post['pirate_sort_order'])) {
            $data['pirate_sort_order'] = $this->request->post['pirate_sort_order'];
        } else {
            $data['pirate_sort_order'] = $this->config->get('pirate_sort_order');
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/pirate.tpl', $data));
    }

    protected function validate() {

        if (!$this->user->hasPermission('modify', 'payment/pirate')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
