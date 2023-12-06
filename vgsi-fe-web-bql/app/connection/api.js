/**
 * Created by duydatpham@gmail.com on Mon Aug 27 2018
 * Copyright (c) 2018 duydatpham@gmail.com
 */

export const langs = {
  en: {
    timeout: "Request timeout",
  },
  vi: {
    timeout: "Hết thời gian yêu cầu. Xin vui lòng thử lại.",
  },
};

export const TIMEOUT_SECOND = 60000;
const isDebuggingInChrome = true; // process.env.NODE_ENV == "development"

export const consoleCustom = (type, url, data, token, language, timeout) => {
  if (isDebuggingInChrome) {
    console.groupCollapsed(
      `%cAPI::${type} ${url}`,
      "color: green; font-weight: bold;"
    );
    console.log("DATA::", JSON.stringify(data));
    console.groupCollapsed("TOKEN::");
    console.log(token);
    console.groupEnd();
    console.log("LANGUAGE::", language);
    console.log("TIMEOUT::", timeout);
    console.groupEnd();
  }
};

const getDebugMode = () => {
  if (window.store) {
    return window.store.getState().showLog.debugMode;
  } else {
    return false;
  }
};

export function POST(url, data, token, apikey, language, timeout) {
  let headers = {
    Accept: "application/json",
    "Content-Type": "application/json",
    "X-Luci-Language": language === "vi" ? "vi-VN" : "en-US",
    "X-Luci-Api-Key": apikey,
    Authorization: `Bearer ${token}`,
    token,
  };
  if (data instanceof FormData) {
    delete headers["Content-Type"];
  }
  if (!token) {
    delete headers.Authorization;
    delete headers.token;
  }
  consoleCustom("POST", url, data, token, language, timeout, headers);
  return new Promise((resolve, reject) => {
    Promise.race([
      new Promise((resl, rej) => {
        setTimeout(resl, timeout || TIMEOUT_SECOND, {
          _isTimeOut: true,
        });
      }),
      fetch(url, {
        headers,
        method: "POST",
        body: data instanceof FormData ? data : JSON.stringify(data),
      }).then((res) => res.json()),
    ])
      .then((json) => {
        console.log("API::POST ", url, json);

        if (!json._isTimeOut) resolve(json);
        else
          resolve({
            success: false,
            message: langs[language] ? langs[language].timeout : "",
          });
      })
      .catch((error) => {
        console.log("error", error);

        reject(error);
      });
  });
}

export function PUT(url, data, token, apikey, language = "vi", timeout) {
  consoleCustom("PUT", url, data, token, language, timeout);

  let headers = {
    Accept: "application/json",
    "Content-Type": "application/json",
    "X-Luci-Language": "vi" ? "vi-VN" : "en-US",
    "X-Luci-Api-Key": apikey,
    Authorization: `Bearer ${token}`,
    token,
  };
  if (!token) {
    delete headers.Authorization;
    delete headers.token;
  }
  return new Promise((resolve, reject) => {
    Promise.race([
      new Promise((resl, rej) => {
        setTimeout(resl, timeout || TIMEOUT_SECOND, {
          _isTimeOut: true,
        });
      }),
      fetch(url, {
        headers,
        method: "PUT",
        body: JSON.stringify(data),
      }).then((res) => res.json()),
    ])
      .then((json) => {
        console.log("value", json);

        if (!json._isTimeOut) resolve(json);
        else
          resolve({
            success: false,
            message: langs[language] ? langs[language].timeout : "",
          });
      })
      .catch((error) => {
        console.log("error", error);

        reject(error);
      });
  });
}

export function parseParams(url, data) {
  let params = url;
  if (data) {
    params += "?";
    let i = 0;
    for (const key in data) {
      if (data[key] !== undefined)
        if (i != 0) params += `&${key}=${data[key]}`;
        else params += `${key}=${data[key]}`;
      i++;
    }
  }
  return params;
}

export function GET(url, data, token, apikey, language = "vi", timeout) {
  let headers = {
    Accept: "application/json",
    "Content-Type": "application/json",
    "X-Luci-Language": "vi" ? "vi-VN" : "en-US",
    "X-Luci-Api-Key": apikey,
    Authorization: `Bearer ${token}`,
    token,
  };
  if (!token) {
    delete headers.Authorization;
  }

  return new Promise((resolve, reject) => {
    let params = url;
    if (data) {
      params += "?";
      let i = 0;
      for (const key in data) {
        if (data[key] !== undefined)
          if (i != 0) params += `&${key}=${data[key]}`;
          else params += `${key}=${data[key]}`;
        i++;
      }
    }

    consoleCustom("GET", params, data, token, language, timeout, headers);
    Promise.race([
      new Promise((resl, rej) => {
        setTimeout(resl, timeout || TIMEOUT_SECOND, {
          _isTimeOut: true,
        });
      }),
      fetch(params, {
        headers,
        method: "GET",
      }).then((res) => res.json()),
    ])
      .then((json) => {
        console.log("API::GET ", url, json);

        if (!json._isTimeOut) resolve(json);
        else
          resolve({
            success: false,
            message: langs[language] ? langs[language].timeout : "",
          });
      })
      .catch((error) => {
        console.log("error", error);

        reject(error);
      });
  });
}
