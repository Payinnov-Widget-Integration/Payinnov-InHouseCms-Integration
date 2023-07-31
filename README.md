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

 # Cms registring retailer in Payinnov ApiGateway

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
Gateway-Payliko-->>-Cms-PlugIn: return { AuthToken, Api Secret Key }
Cms-PlugIn->>+Cms storage: Save Retailer credential + Api Secret Key
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
Note left of Cms-PlugIn: Customer wants to pay with Payliko 
Cms-PlugIn->>+Gateway-Payliko: GET .../healthy
Note right of Gateway-Payliko: Just check Payliko Cms Api Gateway is alive
Gateway-Payliko-->>-Cms-PlugIn: Yes

Cms-PlugIn->>+Payliko-Widget: PlugIn redirect browser to Widget 

Payliko-Widget->>+Gateway-Payliko: Get avaiable blochains/network/coins
Gateway-Payliko-->>-Payliko-Widget: avaiable blochains/network/coins

Payliko-Widget->>+Wallet: Offer transaction to customer
Wallet-->>Payliko-Widget: Customer reject transaction
Payliko-Widget-->>Cms-PlugIn:Redirect to url Cancel Order
Note left of Cms-PlugIn: If CMS is alive and can cancel Order

Wallet-->>-Payliko-Widget:Customer validate transaction
Payliko-Widget->>+Gateway-Payliko: Post OrderId, hashTransaction
Gateway-Payliko-->>-Payliko-Widget:Ok

Payliko-Widget-->>-Cms-PlugIn: Redirect to url Confirm order
Note left of Cms-PlugIn: If CMS is alive and can confirm Order
Cms-PlugIn->>+Gateway-Payliko: Widget connect to
Gateway-Payliko-->>-Cms-PlugIn: AuthToken
Cms-PlugIn->>+Gateway-Payliko: Get /pay/Order?hashTransaction
Note left of Cms-PlugIn: use Bearer AuthToken in https headers
Gateway-Payliko-->>-Cms-PlugIn: OrderId to check
Note left of Cms-PlugIn: Cms confirm order & Cms send e-mail
loop Cms
        Cms-PlugIn->>Cms-PlugIn: Wait for payment validation
end
loop Cms
        Gateway-Payliko-->>Gateway-Payliko: Wait for transaction validation
end

Note left of Gateway-Payliko: Blockchain valide transaction
Gateway-Payliko->>+Cms-PlugIn: GET url Validate payment
Note left of Cms-PlugIn: If CMS is alive and can validate payment 

Cms-PlugIn->>+Gateway-Payliko: Widget connect to
Gateway-Payliko-->>-Cms-PlugIn: AuthToken
Cms-PlugIn->>+Gateway-Payliko: Get /pay/Order?hashTransaction
Note left of Cms-PlugIn: use Bearer AuthToken in https headers
Gateway-Payliko-->>-Cms-PlugIn: OrderId to check
Note left of Cms-PlugIn: Cms validate payment send e-mail
Cms-PlugIn-->>-Gateway-Payliko:Ok
```
