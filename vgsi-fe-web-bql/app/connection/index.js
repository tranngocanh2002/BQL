/**
 * Created by duydatpham@gmail.com on Thu Jul 19 2018
 * Copyright (c) 2018 duydatpham@gmail.com
 * Link API Doc https://api.staging.building.luci.vn/swagger/doc#/
 */

import { notification } from "antd";
import _ from "lodash";
import { logout, saveToken } from "../redux/actions/config";
import * as API from "./api";

const URL_API = process.env.URL_API;

import queryString from "query-string";
import { translateErrorMessage } from "utils";
const selectorToken = (state) => state.get("webState").get("token");
const selectorLanguage = (state) => state.get("language").get("locale");

const selectorTokenRefresh = (state) =>
  state.get("webState").get("refresh_token");
const selectorBuildingCluster = (state) =>
  state.get("config").get("buildingCluster");
const API_KEY = "Y537Z9L6IU67JVOVF5CP";
let currentLanguage = document
  .getElementsByTagName("html")[0]
  .getAttribute("lang");
const io = require("socket.io-client");

export const getFullLinkImage = (url, isMobile = false) => {
  if (!url) return undefined;
  if (url.startsWith("https://")) {
    let newUrl = url.split("uploads");
    return `${URL_API}/uploads${newUrl[1]}`;
  }
  if (url.startsWith(URL_API)) return url;
  return isMobile
    ? `https://customer.tadt.building.luci.vn${url}`
    : `${URL_API}${url}`;
};

export default class Connection {
  static init(store, onComplete) {
    let con = new Connection(store);
    window.connection = con;
  }

  constructor(store) {
    this._token = undefined;
    this._tokenRefresh = undefined;
    this._store = store;
    this._store.subscribe(this.listenerChange);

    this._currentClusterID = undefined;

    this.tokenExpire = _.throttle(
      () => {
        // console.log("tokenExpire");
        this.dispatch(logout());
        this.isCall = false;
      },
      5000,
      { trailing: false }
    );

    this.refreshingToken = false;
    this.isExpired = false;
    this.newToken = null;
    this.isCall = false;
    this.newDataAPI = [];
  }

  listenerChange = () => {
    let token = selectorToken(this._store.getState());
    if (this._token != token) {
      this._token = token;
      if (token) {
        this.isExpired = false;
        this.refreshingToken = false;
      } else {
        this.isExpired = true;
      }
    }
    let tokenRefresh = selectorTokenRefresh(this._store.getState());
    if (this._tokenRefresh != tokenRefresh) {
      this._tokenRefresh = tokenRefresh;
    }
  };

  dispatch = (action) => {
    this._store.dispatch(action);
  };

  getHeader = (contentType) => {
    if (contentType)
      return {
        Accept: "application/json",
        "Content-Type": contentType,
        "X-Luci-Language":
          selectorLanguage(this._store.getState()) === "vi" ? "vi" : "en",
        "X-Luci-Api-Key": API_KEY,
        Authorization: `Bearer ${this._token}`,
      };
    return {
      Accept: "application/json",
      "X-Luci-Language":
        selectorLanguage(this._store.getState()) === "vi" ? "vi" : "en",
      "X-Luci-Api-Key": API_KEY,
      Authorization: `Bearer ${this._token}`,
    };
  };

  POST = (url, data, token = this._token, dontRetry, lang, timeout) => {
    return new Promise((resolve, reject) => {
      let language = selectorLanguage(this._store.getState());
      API.POST(`${URL_API}${url}`, data, token, API_KEY, language, timeout)
        .then((res) => {
          if (!res.success && res.statusCode != 401) {
            notification["error"]({
              placement: "bottomRight",
              duration: 3,
              onClose: () => {},
              message: `${translateErrorMessage(res.message, language)}`,
            });
          }

          if (res.statusCode == 401 && !dontRetry) {
            this.refreshNewToken();
            console.log("this.refreshingToken", this.newDataAPI);
            const intervalCheck = setInterval(() => {
              if (this.refreshingToken) {
                clearInterval(intervalCheck);
                if (this.newDataAPI.includes(url)) {
                  this.POST(url, data, this.newToken).then((res) => {
                    resolve(res);
                  });
                }
              }
            }, 3000);
            // if (this.refreshingToken) {
            //   //Wait to continue
            //   const intervalCheck = setInterval(() => {
            //     if (this.isExpired) {
            //       clearInterval(intervalCheck);
            //       console.log(`stop callAPI::${url}`);
            //       return;
            //     }

            //     if (!this.refreshingToken) {
            //       clearInterval(intervalCheck);
            //       console.log(`continue callAPI::${url}`);
            //       this.POST(url, data, token).then((res) => {
            //         resolve(res);
            //       });
            //     } else {
            //       console.log(`continueWaitting::${url}`);
            //     }
            //   }, 5000);
            // } else {
            //   console.log(
            //     `this.refreshingToken`,
            //     this.refreshingToken,
            //     this._tokenRefresh
            //   );
            //   this.refreshNewToken();
            //   setTimeout(function() {
            //     this.refreshingToken = true;
            //   }, 3000);
            // }
          } else resolve(res);
        })
        .catch((e) => {
          resolve({});
        });
    });
  };
  GET = (url, data, token = this._token, language = "vi", timeout) => {
    return new Promise((resolve, reject) => {
      API.GET(
        `${URL_API}${url}`,
        data,
        token == -1 ? undefined : token,
        API_KEY,
        language,
        timeout
      )
        .then((res) => {
          if (!res.success && res.statusCode != 401) {
            notification["error"]({
              placement: "bottomRight",
              duration: 3,
              onClose: () => {},
              message: `${translateErrorMessage(res.message, language)}`,
            });
          }

          if (res.statusCode == 401) {
            this.refreshNewToken();
            const intervalCheck = setInterval(() => {
              if (this.refreshingToken) {
                clearInterval(intervalCheck);
                if (this.newDataAPI.includes(url)) {
                  console.log("this.refreshingToken22", this.newDataAPI);
                  this.GET(url, data, this.newToken).then((res) => {
                    resolve(res);
                  });
                }
              }
            }, 3000);
            // console.log("this.refreshingToken 111111", this.refreshingToken);
            // if (this.refreshingToken) {
            //   //Wait to continue
            //   const intervalCheck = setInterval(() => {
            //     if (this.isExpired) {
            //       clearInterval(intervalCheck);
            //       console.log(`stop callAPI::${url}`);
            //       return;
            //     }

            //     if (!this.refreshingToken) {
            //       clearInterval(intervalCheck);
            //       console.log(`continue callAPI::${url}`);
            //       this.GET(url, data, token).then((res) => {
            //         resolve(res);
            //       });
            //     } else {
            //       console.log(`continueWaitting::${url}`);
            //     }
            //   }, 10000);
            // } else {
            //   console.log(
            //     `this.refreshingToken`,
            //     this.refreshingToken,
            //     this._tokenRefresh
            //   );
            //   this.refreshNewToken();
            //   setTimeout(function() {
            //     this.refreshingToken = true;
            //   }, 3000);
            // }
          } else resolve(res);
        })
        .catch((e) => {
          resolve({});
        });
    });
  };

  refreshNewToken = (refresh_token = this._tokenRefresh) => {
    let language = selectorLanguage(this._store.getState());

    if (this.isCall === false) {
      this.isCall = true;
      return new Promise((resolve, reject) => {
        API.POST(
          `${URL_API}/auth/refresh-token`,
          { refresh_token },
          undefined,
          API_KEY,
          language
        )
          .then((res) => {
            if (!res.success) {
              notification["error"]({
                placement: "bottomRight",
                duration: 3,
                onClose: () => {},
                message:
                  language === "vi"
                    ? "Hết hạn phiên đăng nhập. Xin vui lòng đăng nhập lại"
                    : "End of login session. Please log in again.",
              });
              this.tokenExpire();
            } else {
              this.dispatch(saveToken(res.data));
              this.refreshingToken = true;
              this.newToken = res.data.access_token;
              this.newDataAPI = Object.values(res.data.auth_group.data_api);
              console.log("this.newDataAPI", this.newDataAPI);
              this.isCall = false;
            }
            resolve(res);
          })
          .catch((e) => {
            notification["error"]({
              placement: "bottomRight",
              duration: 3,
              onClose: () => {},
              message:
                language === "vi"
                  ? "Hết hạn phiên đăng nhập. Xin vui lòng đăng nhập lại"
                  : "End of login session. Please log in again.",
            });
            resolve({});
            this.tokenExpire();
          });
      });
    }
    // return new Promise((resolve, reject) => {
    //   API.POST(
    //     `${URL_API}/auth/refresh-token`,
    //     { refresh_token },
    //     undefined,
    //     API_KEY
    //   )
    //     .then((res) => {
    //       if (!res.success) {
    //         notification["error"]({
    //           placement: "bottomRight",
    //           duration: 3,
    //           onClose: () => {},
    //           message: `Hết hạn phiên đăng nhập. Xin vui lòng đăng nhập lại`,
    //         });
    //         this.tokenExpire();
    //       } else {
    //         this.dispatch(saveToken(res.data));
    //         this.refreshingToken = true;
    //         this.newToken = res.data.access_token;
    //       }
    //       resolve(res);
    //     })
    //     .catch((e) => {
    //       notification["error"]({
    //         placement: "bottomRight",
    //         duration: 3,
    //         onClose: () => {},
    //         message: `Hết hạn phiên đăng nhập. Xin vui lòng đăng nhập lại`,
    //       });
    //       resolve({});
    //       this.tokenExpire();
    //     });
    // });
  };

  getBuildingClusterDetail = (data) => {
    let language = selectorLanguage(this._store.getState());

    return API.POST(
      `${URL_API}/building-cluster/detail`,
      data,
      undefined,
      API_KEY,
      language
    );
  };

  login = (data) => {
    let language = selectorLanguage(this._store.getState());

    return API.POST(
      `${URL_API}/auth/login`,
      data,
      undefined,
      API_KEY,
      language
    );
  };

  getAllPermission = () => {
    return this.GET("/rbac/roles");
  };

  createGroupAuth = (data) => {
    return this.POST("/rbac/create-auth-group", data);
  };

  getGroupAuth = (data) => {
    return this.GET("/rbac/auth-groups", data);
  };

  deleteGroupAuth = (data) => {
    return this.POST("/rbac/auth-group-delete", data);
  };

  getGroupAuthDetail = (data) => {
    return this.GET("/rbac/auth-group-detail", data);
  };

  updateGroupAuthDetail = (data) => {
    return this.POST("/rbac/update-auth-group", data);
  };

  forgotPassword = (data) => {
    return this.POST("/auth/forgot-password", data);
  };

  verifyOTP = (data) => {
    return this.POST("/auth/check-otp-token", data);
  };

  createPassword = (data) => {
    return this.POST("/auth/reset-password", data);
  };

  getCaptcha = () => {
    return this.GET("/auth/generate-captcha");
  };

  createBuildingAreaCreate = (data) => {
    return this.POST("/building-area/create", data);
  };

  updateBuildingAreaCreate = (data) => {
    return this.POST("/building-area/update", data);
  };

  deleteBuildingAreaCreate = (data) => {
    return this.POST("/building-area/delete", data);
  };

  updateBuildingCluster = (data) => {
    return this.POST("/building-cluster/update", data);
  };

  getBuildingArea = (data) => {
    return this.GET("/building-area/list", data);
  };

  createStaff = (data) => {
    return this.POST("/management-user/create", data);
  };

  fetchStaff = (data) => {
    return this.GET("/management-user/list", data);
  };

  fetchCity = (data) => {
    return this.GET("/city/list", data);
  };

  deleteStaff = (data) => {
    return this.POST("/management-user/delete", data);
  };

  updateStaff = (data) => {
    return this.POST("/management-user/update", data);
  };

  fetchDetailStaff = (data) => {
    return this.GET("/management-user/detail", data);
  };

  fetchAllApartment = (data) => {
    return this.GET("/apartment/list", data);
  };

  fetchAllApartmentType = (data) => {
    return this.GET("/apartment/list-type", data);
  };

  deleteApartment = (data) => {
    return this.POST("/apartment/delete", data);
  };

  createApartment = (data) => {
    return this.POST("/apartment/create", data);
  };

  updateApartment = (data) => {
    return this.POST("/apartment/update", data);
  };

  fetchDetailApartment = (data) => {
    return this.GET("/apartment/detail", data);
  };

  fetchMemberOfApartment = (data) => {
    return this.GET("/apartment/list-resident-user", data);
  };

  removeMemberOfApartment = (data) => {
    return this.POST("/apartment/remove-resident-user", data);
  };

  addMemberOfApartment = (data) => {
    return this.POST("/apartment/add-resident-user", data);
  };

  updateTypeMemberOfApartment = (data) => {
    return this.POST("/apartment/update-resident-type", data);
  };

  createResident = (data) => {
    return this.POST("/resident-user/create", data);
  };

  updateResident = (data) => {
    return this.POST("/resident-user/update", data);
  };

  changePhoneResident = (data) => {
    return this.POST("/resident-user/change-phone", data);
  };

  verifyOTPChangePhone = (data) => {
    return this.POST("/resident-user/verify-otp-change-phone", data);
  };

  fetchResident = (data) => {
    return this.GET("/resident-user/list", data);
  };

  fetchResidentByPhone = (data) => {
    return this.GET("/resident-user/list-by-phone", data);
  };

  fetchDetailResident = (data) => {
    return this.GET("/resident-user/detail", data);
  };

  fetchOldResident = (data) => {
    return this.GET("/resident-user/list-old", data);
  };

  fetchDetailOldResident = (data) => {
    return this.GET("/resident-user/detail-old", data);
  };

  fetchApartmentByResident = (data) => {
    return this.GET("/resident-user/list-apartment", data);
  };

  createCategoryTicket = (data) => {
    return this.POST("/request-category/create", data);
  };

  fetchCategoryTicket = (data) => {
    return this.GET("/request-category/list", data);
  };

  deleteCategoryTicket = (data) => {
    return this.POST("/request-category/delete", data);
  };

  updateCategoryTicket = (data) => {
    return this.POST("/request-category/update", data);
  };

  fetchAllTicket = (data) => {
    return this.GET("/request/list", data);
  };

  createNotificationCategory = (data) => {
    return this.POST("/announcement-category/create", data);
  };

  fetchNotificationCategory = (data) => {
    return this.GET("/announcement-category/list", data);
  };

  updateNotificationCategory = (data) => {
    return this.POST("/announcement-category/update", data);
  };

  deleteNotificationCategory = (data) => {
    return this.POST("/announcement-category/delete", data);
  };

  //XXX: 5 window connection method to POST to API
  createNotification = (data) => {
    return this.POST("/announcement-campaign/create", data);
  };

  fetchTotalApartment = (data) => {
    return this.POST("/building-area/count-apartment", data);
  };

  fetchAllNotification = (data) => {
    return this.GET("/announcement-campaign/list", data);
  };

  updateNotification = (data) => {
    return this.POST("/announcement-campaign/update", data);
  };

  deleteNotification = (data) => {
    return this.POST("/announcement-campaign/delete", data);
  };

  extendSurveyDeadline = (data) => {
    return this.POST("/announcement-campaign/extend", data);
  };

  fetchTicketDetail = (data) => {
    return this.GET("/request/detail", data);
  };

  fetchManagerGroups = (data) => {
    return this.GET("/request/list-request-map-auth-group", data);
  };

  updateTicketStatus = (data) => {
    return this.POST("/request/change-status", data);
  };

  addOrRemoveManagerGroup = (data) => {
    return this.POST("/add-or-remove-auth-group", data);
  };

  sendExternalMessage = (data) => {
    return this.POST("/request-answer/create", data);
  };

  fetchExternalMessages = (data) => {
    return this.GET("/request-answer/list", data);
  };

  sendInternalMessage = (data) => {
    return this.POST("/request-answer-internal/create", data);
  };

  fetchInternalMessages = (data) => {
    return this.GET("/request-answer-internal/list", data);
  };

  createTokenDevice = (data) => {
    return this.POST("/management-user-device-token/create", data);
  };

  logout = (data) => {
    return this.POST("/auth/logout", data, undefined, true);
  };

  addProcssGroups = (data) => {
    return this.POST("/request/add-or-remove-auth-group", { ...data, type: 1 });
  };

  removeProcssGroups = (data) => {
    return this.POST("/request/add-or-remove-auth-group", { ...data, type: 0 });
  };

  fetchDetailNotification = (data) => {
    return this.GET("/announcement-campaign/detail", data);
  };

  createServiceProvider = (data) => {
    return this.POST("/service-provider/create", data);
  };

  fetchServiceProvider = (data) => {
    return this.GET("/service-provider/list", data);
  };

  deleteServiceProvider = (data) => {
    return this.POST("/service-provider/delete", data);
  };

  fetchDetailServiceProvider = (data) => {
    return this.GET("/service-provider/detail", data);
  };

  updateServiceProvider = (data) => {
    return this.POST("/service-provider/update", data);
  };

  fetchAllServiceCloud = (data) => {
    return this.GET("/service/list", data);
  };

  fetchDetailServiceCloud = (data) => {
    return this.GET("/service/detail", data);
  };

  addService = (data) => {
    return this.POST("/service-map-management/create", data);
  };

  fetchAllService = (data) => {
    return this.GET("/service-map-management/list", data);
  };

  updateDetailService = (data) => {
    return this.POST("/service-map-management/update", data);
  };

  createPayment = (data) => {
    return this.POST("/service-payment-fee/create", data);
  };

  addIncurredFee = (data) => {
    return this.POST("/service-utility-booking/create-incurred-fee", data);
  };

  fetchDebt = (data) => {
    return this.GET("/service-payment-fee/debt", data);
  };

  fetchAllPayment = (data) => {
    return this.GET("/service-payment-fee/list", data);
  };

  approvePayment = (data) => {
    return this.POST("/service-payment-fee/is-draft", data);
  };

  deletePayment = (data) => {
    return this.POST("/service-payment-fee/delete", data);
  };

  updatePayment = (data) => {
    return this.POST("/service-payment-fee/update", data);
  };

  importPayment = (data) => {
    return this.POST(
      "/service-payment-fee/import",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };

  createBill = (data) => {
    return this.POST("/service-bill/create", data);
  };

  createInvoiceBill = (data) => {
    return this.POST("/service-bill/create-invoice", data);
  };

  updateBill = (data) => {
    return this.POST("/service-bill/update", data);
  };

  updateInvoiceBill = (data) => {
    return this.POST("/service-bill/update-invoice", data);
  };

  blockBill = (data) => {
    return this.POST("/service-bill/block", data);
  };

  updateStatusBill = (data) => {
    return this.POST("/service-bill/change-status", data);
  };

  fetchAllBill = (data) => {
    return this.GET("/service-bill/list", data);
  };

  fetchDetailBill = (data) => {
    return this.GET("/service-bill/detail", data);
  };

  deleteBill = (data) => {
    return this.POST("/service-bill/delete", data);
  };

  cancelBill = (data) => {
    return this.POST("/service-bill/cancel", data);
  };

  dashboardCountAll = (data) => {
    return this.GET("/report/count-all", data);
  };

  dashboardFetchFinance = (data) => {
    return this.GET("/report/service-fee-by-day", data);
  };

  dashboardFetchMaintenance = (data) => {
    return this.GET("/report/count-maintain-equipment", data);
  };

  dashboardFetchRequest = (data) => {
    return this.GET("/report/request-by-day", data);
  };

  dashboardFetchApartment = () => {
    return this.GET("/report/count-apartment");
  };

  dashboardFetchResident = () => {
    return this.GET("/report/count-resident");
  };

  dashboardFetchTotalRevenue = (data) => {
    return this.GET("/report/count-total-revenue", data);
  };

  dashboardBookingRevenue = (data) => {
    return this.GET("/report/service-booking-revenue", data);
  };

  dashboardBookingListRevenue = (data) => {
    return this.GET("/report/service-booking-list-revenue", data);
  };

  fetchNotification = (data) => {
    return this.GET("/management-user-notify/list", data);
  };

  readNotification = (data) => {
    return this.POST("/management-user-notify/is-read", data);
  };

  fetchCountUnreadNotification = (data) => {
    return this.GET("/management-user-notify/count-unread", data);
  };

  fetchDetailServiceBuildingConfig = (data) => {
    return this.GET("/service-building-config/detail", data);
  };

  createServiceBuildingConfig = (data) => {
    return this.POST("/service-building-config/create", data);
  };

  updateServiceBuildingConfig = (data) => {
    return this.POST("/service-building-config/update", data);
  };

  fetchDetailServiceVehicleConfig = (data) => {
    return this.GET("/service-vehicle-config/detail", data);
  };

  createServiceVehicleConfig = (data) => {
    return this.POST("/service-vehicle-config/create", data);
  };

  updateServiceVehicleConfig = (data) => {
    return this.POST("/service-vehicle-config/update", data);
  };

  fetchDetailUser = (data) => {
    return this.GET("/management-user/detail", data);
  };

  updateDetailUser = (data) => {
    return this.POST("/management-user/update-info", data);
  };

  changePasswordUser = (data) => {
    return this.POST("/management-user/change-password", data);
  };

  resetPasswordUser = (data) => {
    return this.POST("/management-user/set-password", data);
  };

  importUser = (data) => {
    return this.POST("/management-user/import", data);
  };

  exportUser = (data) => {
    return this.GET("/management-user/export", data);
  };
  changeStatusStaff = (data) => {
    return this.POST("/management-user/change-status", data);
  };
  downloadFileSample = (data) => {
    return this.POST("/management-user/download-file-sample", data);
  };

  fetchToPrintBilling = (data) => {
    return this.GET("/service-bill/print", data);
  };

  fetchResidentHandbookCategory = (data) => {
    return this.GET("/post-category/list", data);
  };

  addResidentHandbookCategory = (data) => {
    return this.POST("/post-category/create", data);
  };

  updateResidentHandbookCategory = (data) => {
    return this.POST("/post-category/update", data);
  };

  deleteResidentHandbookCategory = (data) => {
    return this.POST("/post-category/delete", data);
  };

  fetchResidentHandbook = (data) => {
    return this.GET("/post/list", data);
  };

  addResidentHandbook = (data) => {
    return this.POST("/post/create", data);
  };

  updateResidentHandbook = (data) => {
    return this.POST("/post/update", data);
  };

  deleteResidentHandbook = (data) => {
    return this.POST("/post/delete", data);
  };

  fetchAllUtilitiIServiceItems = (data) => {
    return this.GET("/service-utility-free/list", data);
  };

  createUtilitiIServiceItems = (data) => {
    return this.POST("/service-utility-free/create", data);
  };

  deleteUtilitiIServiceItems = (data) => {
    return this.POST("/service-utility-free/delete", data);
  };

  updateUtilitiIServiceItems = (data) => {
    return this.POST("/service-utility-free/update", data);
  };

  getDetailUtilitiIServiceItems = (data) => {
    return this.GET("/service-utility-free/detail", data);
  };

  fetchAllConfigUtilityServiceItem = (data) => {
    return this.GET("/service-utility-config/list", data);
  };

  createConfigUtilityServiceItem = (data) => {
    return this.POST("/service-utility-config/create", data);
  };

  updateConfigUtilityServiceItem = (data) => {
    return this.POST("/service-utility-config/update", data);
  };

  createBookingUtility = (data) => {
    return this.POST("/service-utility-booking/create", data);
  };

  fetchAllServiceBookingFee = (data) => {
    return this.GET("/service-booking-fee/list", data);
  };

  fetchDetailBookingUtility = (data) => {
    return this.GET("/service-utility-booking/detail", data);
  };

  fetchBookingUtility = (data) => {
    return this.GET("/service-utility-booking/list", data);
  };

  changeStatusBookingUtility = (data) => {
    return this.POST("/service-utility-booking/change-status", data);
  };

  cancelBookingUtility = (data) => {
    return this.POST("/service-utility-booking/cancel", data);
  };

  checkSlotBookingUtility = (data) => {
    return this.POST("/service-utility-booking/check-slot", data);
  };

  checkPriceBookingUtility = (data) => {
    return this.POST("/service-utility-booking/check-price", data);
  };

  fetchReportByDateBookingUtility = (data) => {
    return this.GET("/service-utility-booking/report-by-date", data);
  };

  fetchConfigPrice = (data) => {
    return this.GET("/service-utility-price/list", data);
  };

  createConfigPrice = (data) => {
    return this.POST("/service-utility-price/create", data);
  };

  deleteConfigPrice = (data) => {
    return this.POST("/service-utility-price/delete", data);
  };

  deleteConfigPlace = (data) => {
    return this.POST("/service-utility-config/delete", data);
  };

  fetchWaterFeeLevel = (data) => {
    return this.GET("/service-water-level/list", data);
  };

  createWaterFeeLevel = (data) => {
    return this.POST("/service-water-level/create", data);
  };

  updateWaterFeeLevel = (data) => {
    return this.POST("/service-water-level/update", data);
  };

  deleteWaterFeeLevel = (data) => {
    return this.POST("/service-water-level/delete", data);
  };

  fetchLastMonthFee = (data) => {
    return this.GET("/service-water-fee/last-index", data);
  };

  fetchDescriptionFee = (data) => {
    return this.POST("/service-water-fee/gen-charge", data);
  };

  fetchWaterFee = (data) => {
    return this.GET("/service-water-fee/list", data);
  };

  createWaterFee = (data) => {
    return this.POST("/service-water-fee/create", data);
  };

  updateWaterFee = (data) => {
    return this.POST("/service-water-fee/update", data);
  };

  deleteWaterFee = (data) => {
    return this.POST("/service-water-fee/delete", data);
  };

  approveWaterFee = (data) => {
    return this.POST("/service-water-fee/change-status", data);
  };

  approveMotoPackingFee = (data) => {
    return this.POST("/service-parking-fee/change-status", data);
  };

  importFeeWater = (data) => {
    return this.POST(
      "/service-water-fee/import",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };

  importVehicle = (data) => {
    return this.POST(
      "/service-management-vehicle/import",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };

  importFeeMotoPacking = (data) => {
    return this.POST(
      "/service-parking-fee/import",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };

  downloadTemplateFeeWater = (data) => {
    return this.GET("/service-water-fee/gen-form", data);
  };

  downloadTemplateFeeMotoPacking = (data) => {
    return this.GET("/service-parking-fee/gen-form", data);
  };

  downloadTemplateManagementVehicle = (data) => {
    return this.GET("/service-management-vehicle/gen-form", data);
  };

  downloadTemplateFee = (data) => {
    return this.GET("/service-payment-fee/gen-form", data);
  };

  fetchMotoPackingFeeLevel = (data) => {
    return this.GET("/service-parking-level/list", data);
  };

  createMotoPackingFeeLevel = (data) => {
    return this.POST("/service-parking-level/create", data);
  };

  updateMotoPackingFeeLevel = (data) => {
    return this.POST("/service-parking-level/update", data);
  };

  deleteMotoPackingFeeLevel = (data) => {
    return this.POST("/service-parking-level/delete", data);
  };

  fetchAllVehicle = (data) => {
    return this.GET("/service-management-vehicle/list", data);
  };

  createVehicle = (data) => {
    return this.POST("/service-management-vehicle/create", data);
  };

  updateVehicle = (data) => {
    return this.POST("/service-management-vehicle/update", data);
  };

  deleteVehicle = (data) => {
    return this.POST("/service-management-vehicle/delete", data);
  };

  activeVehicle = (data) => {
    return this.POST("/service-management-vehicle/active", data);
  };

  cancelVehicle = (data) => {
    return this.POST("/service-management-vehicle/cancel", data);
  };

  fetchMotoPackingFee = (data) => {
    return this.GET("/service-parking-fee/list", data);
  };

  createMotoPackingFee = (data) => {
    return this.POST("/service-parking-fee/create", data);
  };

  updateMotoPackingFee = (data) => {
    return this.POST("/service-parking-fee/update", data);
  };

  deleteMotoPackingFee = (data) => {
    return this.POST("/service-parking-fee/delete", data);
  };

  fetchDescriptionFeeMotoPacking = (data) => {
    return this.POST("/service-parking-fee/gen-charge", data);
  };

  fetchWaterConfig = (data) => {
    return this.GET("/service-water-config/detail", data);
  };

  updateWaterConfig = (data) => {
    return this.POST("/service-water-config/update", data);
  };

  fetchAllBuildingFee = (data) => {
    return this.GET("/service-building-fee/list", data);
  };

  createBuildingFee = (data) => {
    return this.POST("/service-building-fee/create", data);
  };

  updateBuildingFee = (data) => {
    return this.POST("/service-building-fee/update", data);
  };
  deleteBuildingFee = (data) => {
    return this.POST("/service-building-fee/delete", data);
  };
  importBuildingFee = (data) => {
    return this.POST(
      "/service-building-fee/import",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };

  downloadTemplateBuildingFee = (data) => {
    return this.GET("/service-building-fee/gen-form", data);
  };

  approveBuildingFee = (data) => {
    return this.POST("/service-building-fee/change-status", data);
  };

  fetchDescriptionFeeBuildingFee = (data) => {
    return this.POST("/service-building-fee/gen-charge", data);
  };

  importBuildingInfoUsage = (data) => {
    return this.POST(
      "/service-building-info/import",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };

  downloadBuildingTemplateInfoUsage = (data) => {
    return this.GET("/service-building-info/gen-form", data);
  };

  fetchBuildingInfoUsage = (data) => {
    return this.GET("/service-building-info/list", data);
  };

  createBuildingInfoUsage = (data) => {
    return this.POST("/service-building-info/create", data);
  };

  updateBuildingInfoUsage = (data) => {
    return this.POST("/service-building-info/update", data);
  };

  deleteBuildingInfoUsage = (data) => {
    return this.POST("/service-building-info/delete", data);
  };

  importWaterInfoUsage = (data) => {
    return this.POST(
      "/service-water-info/import",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };

  downloadWaterTemplateInfoUsage = (data) => {
    return this.GET("/service-water-info/gen-form", data);
  };

  fetchWaterInfoUsage = (data) => {
    return this.GET("/service-water-info/list", data);
  };

  createWaterInfoUsage = (data) => {
    return this.POST("/service-water-info/create", data);
  };

  updateWaterInfoUsage = (data) => {
    return this.POST("/service-water-info/update", data);
  };

  deleteWaterInfoUsage = (data) => {
    return this.POST("/service-water-info/delete", data);
  };

  fetchBillsForReception = (data) => {
    return this.GET("/service-bill/list-by-receptionist", data);
  };

  fetchAllDebt = (data) => {
    return this.GET("/service-debt/list", data);
  };

  fetchApartmentFeeReminder = (data) => {
    return this.GET("/service-debt/list-reminder", data);
  };

  fetchAllAnnouncementTemplateFee = (data) => {
    return this.GET("/announcement-template/list", data);
  };

  createAnnouncementTemplateFee = (data) => {
    return this.POST("/announcement-template/create", data);
  };

  updateAnnouncementTemplateFee = (data) => {
    return this.POST("/announcement-template/update", data);
  };

  deleteAnnouncementTemplateFee = (data) => {
    return this.POST("/announcement-template/delete", data);
  };

  fetchAnnouncementTemplateFee = (data) => {
    return this.GET("/announcement-template/detail", data);
  };

  fetchAnnouncementFeeApartmentSent = (data) => {
    return this.GET("/announcement-campaign/list-item", data);
  };

  fetchSurveyAnswer = (data) => {
    return this.GET("/announcement-campaign/survey-answer", data);
  };

  fetchAnnouncementApartmentSent = (data) => {
    return this.GET("/announcement-campaign/list-apartment-send-new", data);
  };

  fetchReportChart = (data) => {
    return this.GET("/announcement-campaign/report-chart", data);
  };

  fetchNotificationToPrint = (data) => {
    return fetch(`${URL_API}/read/pdf?${queryString.stringify(data)}`, {
      headers: {
        "X-Luci-Language": "vi",
        "X-Luci-Api-Key": API_KEY,
        Authorization: `Bearer ${this._token}`,
      },
      method: "GET",
    }).then((res) => res.text());
  };

  downloadTemplateApartment = (data) => {
    return this.GET("/apartment/gen-form", data);
  };

  importApartment = (data) => {
    return this.POST(
      "/apartment/import",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };

  downloadTemplateResident = (data) => {
    return this.GET("/resident-user/gen-form", data);
  };

  importResident = (data) => {
    return this.POST(
      "/resident-user/import",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };
  //Electric
  fetchElectricConfig = (data) => {
    return this.GET("/service-electric-config/detail", data);
  };

  updateElectricConfig = (data) => {
    return this.POST("/service-electric-config/update", data);
  };

  importElectricInfoUsage = (data) => {
    return this.POST(
      "/service-electric-info/import",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };

  downloadElectricTemplateInfoUsage = (data) => {
    return this.GET("/service-electric-info/gen-form", data);
  };

  fetchElectricInfoUsage = (data) => {
    return this.GET("/service-electric-info/list", data);
  };

  createElectricInfoUsage = (data) => {
    return this.POST("/service-electric-info/create", data);
  };

  updateElectricInfoUsage = (data) => {
    return this.POST("/service-electric-info/update", data);
  };

  deleteElectricInfoUsage = (data) => {
    return this.POST("/service-electric-info/delete", data);
  };

  fetchElectricFee = (data) => {
    return this.GET("/service-electric-fee/list", data);
  };

  importFeeElectric = (data) => {
    return this.POST(
      "/service-electric-fee/import",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };

  downloadTemplateFeeElectric = (data) => {
    return this.GET("/service-electric-fee/gen-form", data);
  };

  createElectricFee = (data) => {
    return this.POST("/service-electric-fee/create", data);
  };

  approveElectricFee = (data) => {
    return this.POST("/service-electric-fee/change-status", data);
  };

  updateElectricFee = (data) => {
    return this.POST("/service-electric-fee/update", data);
  };

  deleteElectricFee = (data) => {
    return this.POST("/service-electric-fee/delete", data);
  };

  fetchLastMonthElectricFee = (data) => {
    return this.GET("/service-electric-fee/last-index", data);
  };

  fetchDescriptionElectricFee = (data) => {
    return this.POST("/service-electric-fee/gen-charge", data);
  };

  fetchWElectricFeeLevel = (data) => {
    return this.GET("/service-electric-level/list", data);
  };

  createElectricFeeLevel = (data) => {
    return this.POST("/service-electric-level/create", data);
  };

  updateElectricFeeLevel = (data) => {
    return this.POST("/service-electric-level/update", data);
  };

  deleteElectricFeeLevel = (data) => {
    return this.POST("/service-electric-level/delete", data);
  };

  fetchElectricFeeLevel = (data) => {
    return this.GET("/service-electric-level/list", data);
  };

  fetchOldDebitFee = (data) => {
    return this.GET("/service-old-debit-fee/list", data);
  };

  createOldDebitFee = (data) => {
    return this.POST("/service-old-debit-fee/create", data);
  };

  approveOldDebitFee = (data) => {
    return this.POST("/service-old-debit-fee/change-status", data);
  };

  updateOldDebitFee = (data) => {
    return this.POST("/service-old-debit-fee/update", data);
  };

  deleteOldDebitFee = (data) => {
    return this.POST("/service-old-debit-fee/delete", data);
  };

  importFeeOldDebit = (data) => {
    return this.POST(
      "/service-old-debit-fee/import",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };

  downloadTemplateFeeOldDebit = (data) => {
    return this.GET("/service-old-debit-fee/gen-form", data);
  };

  fetchActionControllerLog = (data) => {
    return this.GET("/action-log/action-list", data);
  };

  fetchLogs = (data) => {
    return this.GET("/action-log/list", data);
  };

  fetchPaymentRequests = (data) => {
    return this.GET("/payment/list-code", data);
  };

  deletePaymentRequest = (data) => {
    return this.POST("/payment/del-code", data);
  };

  buildingClusterListSend = (data) => {
    return this.GET("/building-cluster/list-send", data);
  };

  lucidGetAll = (data) => {
    return this.GET("/card-management/list", data);
  };

  lucidApproveCard = (data) => {
    return this.POST("/card-management/approved", data);
  };

  lucidBlockCard = (data) => {
    return this.POST("/card-management/block", data);
  };

  lucidCreateCard = (data) => {
    return this.POST("/card-management/create", data);
  };

  lucidUpdateCard = (data) => {
    return this.POST("/card-management/update", data);
  };

  cardHistory = (data) => {
    return this.GET("/card-management/list-card-history", data);
  };

  identifyHistory = (data) => {
    return this.GET("/resident-user-identification/list-history", data);
  };

  fetchAllNotifySendConfig = () => {
    return this.GET("/notify-send-config/list-detail");
  };

  updateNotifySendConfig = (data) => {
    return this.POST("/notify-send-config/update", data);
  };

  updateAllNotifySendConfig = (data) => {
    return this.POST("/notify-send-config/update-all", data);
  };

  fetchAllNotifyReceiveConfig = () => {
    return this.GET("/management-notify-receive-config/list-detail");
  };

  updateNotifyReceiveConfig = (data) => {
    return this.POST("/management-notify-receive-config/update", data);
  };

  updateAllNotifyReceiveConfig = (data) => {
    return this.POST("/management-notify-receive-config/update-all", data);
  };

  exportApartmentData = (data) => {
    return this.GET("/apartment/export", data, this._token, "vi", 90000);
  };

  exportNotificationDetail = (data) => {
    return this.GET(
      "/announcement-campaign/export-file",
      data,
      this._token,
      "vi",
      90000
    );
  };

  exportResidentData = (data) => {
    return this.GET("/resident-user/export", data, this._token, "vi", 90000);
  };

  exportFinanceFeeData = (data) => {
    return this.GET(
      "/service-payment-fee/export",
      data,
      this._token,
      "vi",
      90000
    );
  };

  exportFinanceBillData = (data) => {
    return this.GET("/service-bill/export", data, this._token, "vi", 90000);
  };

  exportFinanceDebtData = (data) => {
    return this.GET("/service-debt/export", data, this._token, "vi", 90000);
  };

  // Bỏ khi đưa lên product
  createAuthItemWeb = (data) => {
    return this.POST("/rbac/create-auth-item-web", data);
  };

  fetchAllForm = (data) => {
    return this.GET(
      "/service-utility-form/list",
      data,
      this._token,
      "vi",
      90000
    );
  };
  updateStatusForm = (data) => {
    return this.POST("/service-utility-form/change-status", data);
  };
  fetchDetailForm = (data) => {
    return this.GET(
      "/service-utility-form/detail",
      data,
      this._token,
      "vi",
      90000
    );
  };

  fetchContractor = (data) => {
    return this.GET("/contractor/list", data);
  };
  fetchContractorDetail = (data) => {
    return this.GET("/contractor/detail", data);
  };
  createContractor = (data) => {
    return this.POST("/contractor/create", data);
  };
  updateContractor = (data) => {
    return this.POST("/contractor/update", data);
  };
  deleteContractor = (data) => {
    return this.POST("/contractor/delete", data);
  };

  fetchAllTask = (data) => {
    return this.GET("/job/list", data);
  };
  fetchDetailTask = (data) => {
    return this.GET("/job/detail", data);
  };
  fetchTaskComments = (data) => {
    return this.GET("/job-comment/list", data);
  };
  changeTaskStatus = (data) => {
    return this.POST("/job/change-status", data);
  };
  deleteTask = (data) => {
    return this.POST("/job/delete", data);
  };
  createTaskComment = (data) => {
    return this.POST("/job-comment/create", data);
  };
  createTask = (data) => {
    return this.POST("/job/create", data);
  };
  updateTask = (data) => {
    return this.POST("/job/update", data);
  };
  fetchMaintainList = (data) => {
    return this.GET("/maintenance-device/list", data);
  };
  importMaintainList = (data) => {
    return this.POST(
      "/maintenance-device/import-form",
      data,
      this._token,
      false,
      "vi",
      90000
    );
  };
  downloadTemplateMaintainList = (data) => {
    return this.GET("/maintenance-device/gen-form", data);
  };
  createEquipment = (data) => {
    return this.POST("/maintenance-device/create", data);
  };
  fetchDetailEquipment = (data) => {
    return this.GET("/maintenance-device/detail", data);
  };
  updateEquipment = (data) => {
    return this.POST("/maintenance-device/update", data);
  };
  deleteEquipment = (data) => {
    return this.POST("/maintenance-device/delete", data);
  };
  fetchMaintainScheduleList = (data) => {
    return this.GET("/maintenance-device/list-schedule", data);
  };
  exportMaintainDevicesList = (data) => {
    return this.GET("/maintenance-device/export", data);
  };
  exportMaintainScheduleList = (data) => {
    return this.GET("/maintenance-device/export-schedule", data);
  };
  createMaintainSchedule = (data) => {
    return this.POST("/maintenance-device/confirmation", data);
  };
  changeCombineCardStatus = (data) => {
    return this.POST("/card-management/change-status", data);
  };
  deleteCombineCard = (data) => {
    return this.POST("/card-management/delete", data);
  };
  createCombineCard = (data) => {
    return this.POST("/card-management/create", data);
  };
  updateCombineCard = (data) => {
    return this.POST("/card-management/update", data);
  };
  fetchAllCombineCard = (data) => {
    return this.GET("/card-management/list", data);
  };
  fetchDetailCombineCard = (data) => {
    return this.GET("/card-management/detail", data);
  };
  downloadTemplateCombineCard = (data) => {
    return this.GET("/card-management/gen-form", data);
  };
  exportCombineCardData = (data) => {
    return this.GET("/card-management/export", data);
  };
  createActiveCard = (data) => {
    return this.POST("/card-management/approved", data);
  };
}
