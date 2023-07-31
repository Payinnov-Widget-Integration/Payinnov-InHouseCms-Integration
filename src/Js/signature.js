// https://nodejs.org/api/crypto.html
const crypto = require("crypto");
/**
 * Compute headers.signature of an HTTPS request
 * @param retailerSecretKey retailer Secret API Key
 * @param endpoint called sample: "/api/v1/pay/Order"
 * @param payload  body of POST request
 * @returns signature
 */
function signature(retailerSecretKey, endpoint, payload) {
    // Unix timestamp convert to string : https://www.unixtimestamp.com/
    // https://developer.mozilla.org/fr/docs/Web/JavaScript/Reference/Global_Objects/Date/now
    const time = Date.now().toString();
    const signature = crypto.createHmac('sha256', retailerSecretKey);
    const data = {
        date: time,
        endpoint,
        payload
    };
    const jsonData = JSON.stringify(data);
    signature.update( jsonData );
    return `${time}:${signature.digest('base64')}`;
}
