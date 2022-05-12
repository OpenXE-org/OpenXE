# Post.at

Provides integration with the Austrian post office, Österreichische Post AG.

The module is able to communicate with their Post-Labelcenter (https://plc.post.at/)
which is an online services that provides features for their business customers.

With Post-Labelcenter it is possible can create parcel labels, mailing lists and
the necessary notification data for logistical processing.

See also https://xentral.atlassian.net/l/c/tVLHRY7Z

## API methods

Only a couple of the available API methods have currently been implemented:

 - `GetAllowedServicesForCountry`:
   - Get list of postal services available for the given countries
 - `ImportShipment`:
   - Generates a shipping label for the given package and chosen delivery type

## Example usage

```php
class Example {
    public function __construct(SoapServiceFactory $client)
    {
        $this->clientFactory = $client;
    }

    public function example($shippingMethodConfig)
    {
        try {
            $response = $this->clientFactory
                ->fromConfigArray($shippingMethodConfig)
                ->getAllowedServicesForCountry(['de']);

        } catch (\Xentral\Modules\Postat\SOAP\PostAtException $exception) {
            // Display the potential error message to the end user.
        }
    }
}
```

Failed API call throws a `PostAtException`. The message in the exception is
always user-friendly and should be displayed to the end user.
