<?php


namespace Geega\Micro;


abstract class Service
{
    /**
     * HTTP Get
     *
     * @param $method
     * @param array $params
     *
     * @return mixed
     */
    static public function get($method, $params = [])
    {
        $service = new static;
        return $service->executeGet($method, $params);
    }

    /**
     * HTTP post
     *
     * @param  $method
     * @param  array $params
     * @return mixed
     */
    static public function post($method, $params = [], $queryParams = [])
    {
        $service = new static;
        return $service->executePost($method, $params, $queryParams);
    }

    /**
     * Execute post request
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public function executePost($method, $formData, $queryParams = [])
    {
        $url = $this->getUrl($method);

        if (!empty($queryParams)) {
            $url .= '?'.http_build_query($queryParams);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);

        curl_close($ch);

        $output = json_decode($output, true);
        
        return $output;
    }

    /**
     * Execute get request
     *
     * @param  $method
     * @param  $params
     * @return mixed
     */
    public function executeGet($method, $params)
    {
        $url = $this->getUrl($method);
        if (!empty($params)) {
            $url .= '?'.http_build_query($params);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output, true);
        return $output;
    }


    /**
     * Get url for request
     *
     * @param  $method
     * @return string
     */
    public function getUrl($method)
    {
        $count_connector = count($this->getConnectors());
        $rand_connector = rand(0, $count_connector) % $count_connector;
        $rand_connector = $this->getConnectors()[$rand_connector];
        return 'http://'.$rand_connector['host'].':'.$rand_connector['port'].$this->getMethods()[$method];
    }

    /**
     * Get connectors
     * 
     * @return array
     */
    abstract public function getConnectors();

    /**
     * Get methods
     *
     * @return array
     */
    abstract public function getMethods();
}