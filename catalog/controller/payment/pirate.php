<?php

class ControllerPaymentPirate extends Controller {

    private $_pirate;

    public function __construct($registry) {

        parent::__construct($registry);

        $this->load->language('payment/pirate');
        $this->load->model('checkout/order');

        $this->_pirate = new Pirate(
            $this->config->get('pirate_host'),
            $this->config->get('pirate_port'),
            $this->config->get('pirate_user'),
            $this->config->get('pirate_password')
        );
    }

    public function index() {

$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
$total = $this->currency->format($order_info['total'], $this->config->get('pirate_currency'));
$total = filter_var($total, FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
$note = (string)$this->session->data['order_id'];

        $data['text_instruction'] = $this->language->get('text_instruction');
        $data['text_loading']     = $this->language->get('text_loading');
        $data['text_description'] = sprintf($this->language->get('text_description'),
                                            $total,
                                            $this->config->get('pirate_currency'));

        $data['button_confirm']   = $this->language->get('button_confirm');
        $data['continue']         = $this->url->link('checkout/success');

        $result = $this->_pirate->z_getnewaddress();

if (isset($result['result'])) {
            $data['address'] = $this->session->data['pirate'] = $result['result'];

        } else {

            $data['address'] = $this->session->data['pirate'] = false;

            if (isset($result['error']['message']) && isset($result['error']['code'])) {
                $this->log->write($result['error']['message'] . '/' . $result['error']['code']);
            } else {
                $this->log->write('Could not receive Pirate address');
            }
        }

        if ($data['address']) {
            switch ($this->config->get('pirate_qr')) {

                case 'google':
                    $data['qr'] = 'https://chart.googleapis.com/chart?chs=240x240&cht=qr&chl=' . $data['address'];
                    break;

                default:
                    $data['qr'] = false;
            }
        }

        return $this->load->view('payment/pirate.tpl', $data);
    }

    public function confirm() {

        if ($this->session->data['payment_method']['code'] == 'pirate' && isset($this->session->data['pirate'])) {

            $this->model_checkout_order->addOrderHistory(
                $this->session->data['order_id'],
                $this->config->get('pirate_order_status_id'),
                sprintf(
                    $this->language->get('text_pirate_address'),
                    $this->session->data['pirate']
                ),
                true
            );

            unset($this->session->data['pirate']);
        }
    }
}
