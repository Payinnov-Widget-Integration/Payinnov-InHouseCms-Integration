# Backend Payliko
Inhouse CMS integration

<p align="center">
<img
    alt="PayInnov"
    src="./assets/logo.svg"
    height="200"
/><br>
Copyright (c) 2020-2023 PayInnov
</p>

#

<p>
  <a href="./LICENSE">
      <img
        alt="license:MIT"
        src="https://img.shields.io/badge/License-MIT-blue"
      />
  </a>
  <img
      alt="Language:TypeScript"
      src="https://img.shields.io/badge/Language-TypeScript-purple"
  />
  <img
      alt="Language:Php"
      src="https://img.shields.io/badge/Language-PHP-purple"
  />
</p>
</p>

# Prerequires

To implement integration of Payinnov crypto currency paiment in a inhouse CMS you need an PayInnov merchant account to get:
 - Uuid string as a Retailer'certificate
 - API Secret Key

 # Swagger of gateway-cms

 https://tests.payliko-demo.fr/gateway-cms/documentation/static/index.html

 # Cms registering a retailer in Payinnov ApiGateway

Use https://mermaid-js.github.io/mermaid-live-editor to update graphs.

*Sequence diagram*

```mermaid
sequenceDiagram
actor A as Retailer Admin

A ->>+Cms-PlugIn: Cms admin save Retailer credentials with password
Cms-PlugIn->>+Gateway-Payliko: GET .../healthy
Note right of Gateway-Payliko: Just check Payliko Cms Api Gateway is alive
Gateway-Payliko-->>-Cms-PlugIn: Yes

Cms-PlugIn->>+Gateway-Payliko: POST .../user/ConnectRetailer
Note right of Gateway-Payliko: Api Gateway chechek Retailer credential and user password
Gateway-Payliko-->>-Cms-PlugIn: return { AuthToken, ApiSecretKey }
Cms-PlugIn->>+Cms storage: Save RetailerCredential + ApiSecretKey
Cms storage-->>-Cms-PlugIn: Ok

Cms-PlugIn->>+Gateway-Payliko: POST .../user/Retailer
Note left of Cms-PlugIn: use Bearer AuthToken in https headers

Note right of Gateway-Payliko: Gateway use "CmsDomain" to validate futur https requests origin
Gateway-Payliko-->>-Cms-PlugIn: Ok
Cms-PlugIn-->>-A: Ok
```
## Cms payment with Payinnov ApiGateway

*Sequence diagram*

```mermaid
sequenceDiagram
actor A as Customer
A->>+Cms-PlugIn: Customer wants to pay with Payliko 
Cms-PlugIn->>+Gateway-Payliko: POST .../Ordre
Note right of Gateway-Payliko: Https request is signed with ApiSecretKey
Gateway-Payliko-->>-Cms-PlugIn: redirection WidgetUrl include JWT authentification

Cms-PlugIn->>+Payliko-Widget: PlugIn redirect browser to WidgetUrl 

Payliko-Widget->>+Gateway-Payliko: GET available blockcahins, coins, rates
Note right of Gateway-Payliko: Https request is authentified by JWT
Gateway-Payliko-->>-Payliko-Widget: available blochains, coins, rates

Payliko-Widget->>+Wallet: Offer transaction to customer
Wallet-->>Payliko-Widget: Customer reject transaction
Payliko-Widget-->>Cms-PlugIn:Redirect to url Cancel Order
Note left of Cms-PlugIn: If CMS is alive and can cancel Order
Cms-PlugIn->>+Payliko-Widget: POST .../CancelOrdre
Note right of Gateway-Payliko: Https request is signed with ApiSecretKey
Payliko-Widget->>-Cms-PlugIn:Ok
Cms-PlugIn-->>A: redirected to order canceled page

Wallet-->>-Payliko-Widget:Customer validate transaction
Payliko-Widget->>+Gateway-Payliko: POST hashTransaction to follow
Note right of Gateway-Payliko: Https request is authentified by JWT
Gateway-Payliko-->>Payliko-Widget:Ok

Payliko-Widget-->>-Cms-PlugIn: Redirect to url Confirm order
Cms-PlugIn-->>-A: redirected to order confirmed page

loop Following blockchain transaction
    Gateway-Payliko-->>Gateway-Payliko: Wait for transaction status changes

    Note left of Gateway-Payliko: blockchain transaction status: initialised, mined, validated / canceled

    loop until Cms-PlugIn HTTP response status code is 200
        Gateway-Payliko->>+Cms-PlugIn: POST webHook
        Note left of Gateway-Payliko: Https request is signed with ApiSecretKey
        Cms-PlugIn-->>-Gateway-Payliko:HTTP response status code
    end

    deactivate Gateway-Payliko
end
```
