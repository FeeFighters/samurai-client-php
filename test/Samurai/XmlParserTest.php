<?php

require_once realpath(dirname(__FILE__)) . '/../TestHelper.php';

class Samurai_XmlParserTest extends PHPUnit_Framework_TestCase
{
	public function setUp() {
		$sampleSamuraiXML = <<<XML
		<payment_method>
			<payment_method_token>2a492618486f55b73d06ed8b</payment_method_token>
			<created_at type="datetime">2011-11-06T15:05:43.000Z</created_at>
			<updated_at type="datetime">2011-11-06T15:05:43.000Z</updated_at>
			<custom></custom>
			<is_retained type="boolean">true</is_retained>
			<is_redacted type="boolean">false</is_redacted>
			<is_sensitive_data_valid type="boolean">true</is_sensitive_data_valid>
			<is_expiration_valid type="boolean">true</is_expiration_valid>
			<processor_response>
				<success type="boolean">false</success>
				<messages type="array">
					<message class="error" context="processor.avs" key="country_not_supported" />
					<message class="error" context="input.cvv" key="too_short" />
				</messages>
			</processor_response>
			<messages type="array"></messages>
			<last_four_digits>1111</last_four_digits>
			<card_type></card_type>
			<first_name>sean</first_name>
			<last_name>harper</last_name>
			<expiry_month type="integer">3</expiry_month>
			<expiry_year type="integer">2015</expiry_year>
			<address_1>1240 W Monroe #1</address_1>
			<address_2></address_2>
			<city>Chicago</city>
			<state>IL</state>
			<zip>53211</zip>
			<country></country>
		</payment_method>
XML;

		$this->sampleSamuraiJSON = array(
			'payment_method' => array(
				'payment_method_token'    => '2a492618486f55b73d06ed8b',
				'created_at'              => '2011-11-06T15:05:43.000Z',
				'updated_at'              => '2011-11-06T15:05:43.000Z',
				'custom'                  => '',
				'is_retained'             => true,
				'is_redacted'             => false,
				'is_sensitive_data_valid' => true,
				'is_expiration_valid'     => true,
				'processor_response'      => array(
					'success'  => false,
					'messages' => array(
						array('class' => 'error', 'context' => 'processor.avs', 'key' => 'country_not_supported'),
						array('class' => 'error', 'context' => 'input.cvv',     'key' => 'too_short')
					)
				),
				'messages'         => array(),
				'last_four_digits' => '1111',
				'card_type'        => '',
				'first_name'       => 'sean',
				'last_name'        => 'harper',
				'expiry_month'     => 3,
				'expiry_year'      => 2015,
				'address_1'        => '1240 W Monroe #1',
				'address_2'        => '',
				'city'             => 'Chicago',
				'state'            => 'IL',
				'zip'              => '53211',
				'country'          => ''
			)
		);

		$this->parsedResponse = Samurai_XmlParser::parse($sampleSamuraiXML);
	}

	public function testParse() {
		$this->assertEquals($this->sampleSamuraiJSON, $this->parsedResponse);
	}
}
