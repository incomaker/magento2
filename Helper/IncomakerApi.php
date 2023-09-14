<?php

namespace Incomaker\Magento2\Helper;

use Incomaker\Api\Connector;
use Incomaker\Api\Data\Event;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Psr\Log\LoggerInterface;

class IncomakerApi {

	private CookieManagerInterface $cookieManager;
	private AddressRepositoryInterface $addressRepository;
	private Connector $incomaker;
	private LoggerInterface $logger;
	private $eventController;
	private $contactController;

	public function __construct(
		CookieManagerInterface $cookieManager,
		Connector $api,
		AddressRepositoryInterface $addressRepository,
		LoggerInterface $logger
	) {
		$this->cookieManager = $cookieManager;
		$this->incomaker = $api;
		$this->addressRepository = $addressRepository;
		$this->logger = $logger;
	}

	public function getPermId() {
		if ($this->cookieManager->getCookie("incomaker_p") != null) {
			return $this->cookieManager->getCookie("incomaker_p");
		}
		return $this->cookieManager->getCookie("permId");
	}

	/**
	 * Checks whether plugin settings values are okay and logs a warning when not.
	 * @return boolean True if plugin settings are set up
	 */
	public function checkSettings() {
		if ($this->incomaker->isSettingsOk()) return true;
		$this->logger->warning("Incomaker plugin is not properly configured! Make sure you set values of API key, Account ID and Plugin ID on plugin configuration page.");
		return false;
	}

	public function getCampaignId() {
		return $this->cookieManager->getCookie("incomaker_c");
	}

	public function postEvent($event, $customer) {
		if (!$this->checkSettings()) return;

		$event = new Event($event, $this->getPermId());

		if (isset($customer)) {
			$event->setClientContactId($customer->getId());
		}
		if (!isset($this->eventController)) {
			$this->eventController = $this->incomaker->createEventController();
		}
		$this->eventController->addEvent($event);
	}

	public function postProductEvent($event, $customerId, $productId, $sessionId) {

		$this->logger->warning("Sleeping 5 secs");
		sleep(5);

		if (!$this->checkSettings()) return;

		$event = new Event($event, $this->getPermId());

		if (!empty($customerId)) {
			$event->setClientContactId($customerId);
		}
		if (!empty($productId)) {
			$event->setRelatedId($productId);
		}
		if (!empty($sessionId)) {
			$event->setSessionId($sessionId);
		}
		if (!isset($this->eventController)) {
			$this->eventController = $this->incomaker->createEventController();
		}
		$this->eventController->addEvent($event);
	}

	public function postOrderEvent($event, $customer, $total, $session) {
		if (!$this->checkSettings()) return;

		$event = new Event($event, $this->getPermId());

		if (isset($customer)) {
			$event->setClientContactId($customer);
		}
		$event->setCampaignId($this->getCampaignId());  //TODO remove passing campaignId this way
		$event->addCustomField("total", $total);
		if (!empty($session)) {
			$event->setSessionId($session);
		}
		if (!isset($this->eventController)) {
			$this->eventController = $this->incomaker->createEventController();
		}
		$this->eventController->addEvent($event);
	}

	public function addContact(\Magento\Customer\Model\Data\Customer $customer) {
		if (!$this->checkSettings()) return;

		if (!isset($this->contactController)) {
			$this->contactController = $this->incomaker->createContactController();
		}

		$contact = new \Incomaker\Api\Data\Contact($customer->getId());
		$contact->setPermId($this->getPermId());
		$contact->setFirstName(htmlspecialchars($customer->getFirstname()));
		$contact->setLastName(htmlspecialchars($customer->getLastname()));
		$contact->setEmail($customer->getEmail());
		$contact->setBirthday($customer->getDob());

		$customerId = $customer->getDefaultBilling();
		if (isset($customerId)) {
			$billingAddress = $this->addressRepository->getById($customerId);
			if ($billingAddress != false) {
				$contact->setCompanyName(htmlspecialchars($billingAddress->getCompany()));
				if (isset($billingAddress->getStreet()[0])) {
					$contact->setStreet(htmlspecialchars($billingAddress->getStreet()[0]));
				}

				$contact->setZipCode(htmlspecialchars($billingAddress->getPostcode()));
				$contact->setCity(htmlspecialchars($billingAddress->getCity()));
				$contact->setPhoneNumber1($billingAddress->getTelephone());
				$contact->setPhoneNumber2($billingAddress->getFax());
				$contact->setCountry(strtolower($billingAddress->getCountryId()));
			}
		}

		$this->contactController->addContact($contact);

	}
}
