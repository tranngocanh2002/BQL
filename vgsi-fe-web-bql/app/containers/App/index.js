/**
 *
 * App.js
 *
 * This component is the skeleton around the actual pages, and should only
 * contain code that should be seen on all pages. (e.g. navigation bar)
 *
 */

import React from "react";
import { Redirect, Route, Switch, withRouter } from "react-router-dom";

import { ConfigProvider } from "antd";
import en_GB from "antd/lib/locale-provider/en_GB";
import vi_VN from "antd/lib/locale-provider/vi_VN";
import HomePage from "containers/Home";
import BuildingInfomationPage from "../Dashboard/BuildingInfomation/Loadable";
import StaffAddPage from "../StaffManagementContainer/StaffAdd/Loadable";
import StaffDetailPage from "../StaffManagementContainer/StaffDetail/Loadable";
import StaffListPage from "../StaffManagementContainer/StaffList/Loadable";

import ApartmentAddPage from "../ApartmentContainer/ApartmentAdd/Loadable";
import ApartmentDetailPage from "../ApartmentContainer/ApartmentDetail/Loadable";
import ApartmentListPage from "../ApartmentContainer/ApartmentList/Loadable";
import ActionLogSystemPage from "../SettingContainer/ActionLogSystem/Loadable";
import BuildingClusterInfomationPage from "../SettingContainer/BuildingClusterInfomation/Loadable";
import NotificationFeeManagerPage from "../SettingContainer/NotificationFeeManager/Loadable";
import NotifyReceiveConfigPage from "../SettingContainer/NotifyReceiveConfig/Loadable";
import NotifySendConfigPage from "../SettingContainer/NotifySendConfig/Loadable";
import ResidentHandbookPage from "../SettingContainer/ResidentHandbook/Loadable";
import RolesPage from "../SettingContainer/Roles/Loadable";
import RolesSettingPage from "../SettingContainer/RolesSetting/Loadable";
import SettingPlanePage from "../SettingContainer/SettingPlane/Loadable";
import SetupNotificationPage from "../SettingContainer/SetupNotification/Loadable";

import ResidentAddPage from "../ResidentContainer/ResidentAdd/Loadable";
import ResidentDetailPage from "../ResidentContainer/ResidentDetail/Loadable";
import ResidentListPage from "../ResidentContainer/ResidentList/Loadable";

import TicketCategoryPage from "../TicketContainer/TicketCategory/Loadable";
import TicketDetailPage from "../TicketContainer/TicketDetail/Loadable";
import TicketListPage from "../TicketContainer/TicketList/Loadable";

import NotificationAddPage from "../NotificationContainer/NotificationAdd/Loadable";
import NotificationCategoryPage from "../NotificationContainer/NotificationCategory/Loadable";
import NotificationDetailPage from "../NotificationContainer/NotificationDetail/Loadable";
import NotificationFeePage from "../NotificationContainer/NotificationFee/Loadable";
import NotificationListPage from "../NotificationContainer/NotificationList/Loadable";
import NotificationUpdatePage from "../NotificationContainer/NotificationUpdate/Loadable";

import ServiceAddPage from "../ServiceContainer/ServiceAdd/Loadable";
import ServiceCloudPage from "../ServiceContainer/ServiceCloud/Loadable";
import ServiceFeeCreatePage from "../ServiceContainer/ServiceFeeCreate/Loadable";
import ServiceListPage from "../ServiceContainer/ServiceList/Loadable";
import ServiceProviderPage from "../ServiceContainer/ServiceProvider/Loadable";
import ServiceProviderAddPage from "../ServiceContainer/ServiceProviderAdd/Loadable";
import ServiceProviderDetailPage from "../ServiceContainer/ServiceProviderDetail/Loadable";
import ResourceManagerPage from "../SettingContainer/ResourceManager/Loadable";

// Setting Services
import SettingServiceAddPage from "../SettingContainer/ServiceConfig/ServiceAdd/Loadable";
import SettingServiceCloudPage from "../SettingContainer/ServiceConfig/ServiceCloud/Loadable";
import SettingServiceListPage from "../SettingContainer/ServiceConfig/ServiceList/Loadable";

import AccountContainerPage from "../AccountContainer/Loadable";
import BillListPage from "../FinanceContainer/BillList/Loadable";
import CancelBillListPage from "../FinanceContainer/CancelBillList/Loadable";
import CashBookPage from "../FinanceContainer/CashBook/Loadable";
import DebtPage from "../FinanceContainer/DashboardDebt/Loadable";
import DashboardDebtAllPage from "../FinanceContainer/DashboardDebtAll/Loadable";
import NotificationFeeDetailPage from "../FinanceContainer/NotificationFeeDetail/Loadable";
import NotificationFeeListPage from "../FinanceContainer/NotificationFeeList/Loadable";
import NotificationFeeUpdatePage from "../FinanceContainer/NotificationFeeUpdate/Loadable";
import RequestPaymentPage from "../FinanceContainer/RequestPayment/Loadable";
import RevenueByMonth from "../FinanceContainer/RevenueByMonth/Loadable";

import MainLayout from "../MainLayout/Loadable";
import CreatePasswordPage from "../UserContainer/CreatePassword/Loadable";
import ForgotPasswordPage from "../UserContainer/ForgotPassword/Loadable";
import LoginPage from "../UserContainer/Login/Loadable";
import LoginByToken from "../UserContainer/LoginByToken/Loadable";
import UserContainer from "../UserContainer/Main/Loadable";

import HistoryAccessControlPage from "../HistoryAccessControl/Loadable";
import HistoryCarpackingPage from "../HistoryCarpacking/Loadable";

import NotFoundPage from "containers/NotFoundPage";

import GlobalStyle from "../../global-styles";

import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import Loader from "../../components/Loader/Loader";
import {
  selectBuildingCluster,
  selectInited,
  selectToken,
} from "../../redux/selectors";

import RequestPaymentDetail from "containers/FinanceContainer/RequestPaymentDetail/Loadable";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import MaintainAdd from "containers/MaintainDevices/MaintainAdd/Loadable";
import MaintainDetail from "containers/MaintainDevices/MaintainDetail/Loadable";
import MaintainList from "containers/MaintainDevices/MaintainList/Loadable";
import TaskAdd from "containers/TaskContainer/TaskAdd/Loadable";
import TaskDetail from "containers/TaskContainer/TaskDetail/Loadable";
import TaskEdit from "containers/TaskContainer/TaskEdit/Loadable";
import TaskList from "containers/TaskContainer/TaskList/Loadable";
import moment from "moment";
import "moment/locale/en-gb";
import "moment/locale/vi";
import ModalImport from "../../components/ModalImport";
import BookingAddPage from "../Dashboard/BookingAddPage";
import BookingDetailPage from "../Dashboard/BookingDetailPage";
import DashboardBillDetailPage from "../Dashboard/DashboardBillDetail/Loadable";
import DashboardBookingListPage from "../Dashboard/DashboardBookingList/Loadable";
import DashboardInvoiceBillPage from "../Dashboard/DashboardInvoiceBill/Loadable";
import DashboardInvoiceBillDetailPage from "../Dashboard/DashboardInvoiceBillDetail/Loadable";
import DashboardReceptionPage from "../Dashboard/DashboardReception/Loadable";
import CancelInvoiceBillListPage from "../FinanceContainer/CancelInvoiceBillList/Loadable";
import InvoiceBillListPage from "../FinanceContainer/InvoiceBillList/Loadable";
import FormDetail from "../FormContainer/FormDetail";
import FormList from "../FormContainer/FormList";
import ContractorAdd from "../SupplierContainer/SupplierAdd";
import ContractorDetail from "../SupplierContainer/SupplierDetail";
import ContractorPage from "../SupplierContainer/SupplierList/Loadable";
import ResetPasswordPage from "../UserContainer/ResetPassword";
import VerifyOTPPage from "../UserContainer/VerifyOTP";
import CombineCardList from "containers/CombineCardContainer/CombineCardList";
import CombineCardDetail from "containers/CombineCardContainer/CombineCardDetail";
import CombineCardActive from "containers/CombineCardContainer/CombineCardActive";

class App extends React.PureComponent {
  render() {
    const { isInited, currentToken, language } = this.props;
    moment.locale(language === "en" ? "en-gb" : "vi");
    if (!isInited) {
      return <Loader spinning />;
    }
    return (
      <ConfigProvider locale={language === "en" ? en_GB : vi_VN}>
        <div>
          <Switch>
            <Route exact path="/login_by_token" component={LoginByToken} />
            <Route
              exact
              path="/iot"
              render={() => (
                <div
                  style={{
                    padding: 20,
                  }}
                >
                  <HomePage />
                </div>
              )}
            />
            <Route
              exact
              path="/user/:mode"
              render={() => {
                return (
                  <UserContainer>
                    <Switch>
                      <Route exact path="/user/login" component={LoginPage} />
                      <Route
                        exact
                        path="/user/forgotpassword"
                        component={ForgotPasswordPage}
                      />
                      <Route
                        exact
                        path="/user/verifyOtp"
                        component={VerifyOTPPage}
                      />
                      <Route
                        exact
                        path="/user/resetpassword"
                        component={ResetPasswordPage}
                      />
                      <Route
                        exact
                        path="/user/createpassword"
                        component={CreatePasswordPage}
                      />
                      <Route
                        render={() => <NotFoundPage redirect={"/user/login"} />}
                      />
                    </Switch>
                  </UserContainer>
                );
              }}
            />
            <Route
              path="/main/:mode"
              render={() => {
                return (
                  <MainLayout>
                    <Switch>
                      <Route exact path="/main/home" component={HomePage} />
                      <Route
                        exact
                        path="/main/infomation"
                        component={BuildingInfomationPage}
                      />
                      <Route
                        exact
                        path="/main/bookinglist"
                        component={DashboardBookingListPage}
                      />
                      <Route
                        exact
                        path="/main/bookinglist/detail/:id/info"
                        component={BookingDetailPage}
                      />
                      <Route
                        exact
                        path="/main/bookinglist/add"
                        component={BookingAddPage}
                      />
                      <Route
                        exact
                        path="/main/apartment/list"
                        component={ApartmentListPage}
                      />
                      <Route
                        exact
                        path="/main/apartment/detail/:id"
                        component={ApartmentDetailPage}
                      />
                      <Route
                        exact
                        path="/main/apartment/add"
                        render={() => (
                          <ApartmentAddPage key="addPage-apartment" />
                        )}
                      />
                      <Route
                        exact
                        path="/main/apartment"
                        render={() => <Redirect to="/main/apartment/list" />}
                      />
                      <Route
                        exact
                        path="/main/resident/list"
                        component={ResidentListPage}
                      />
                      <Route
                        exact
                        path="/main/resident/detail/:id"
                        component={ResidentDetailPage}
                      />
                      <Route
                        exact
                        path="/main/resident/add"
                        component={ResidentAddPage}
                      />
                      <Route
                        exact
                        path="/main/resident"
                        render={() => <Redirect to="/main/resident/list" />}
                      />
                      {/* <Route
                      exact
                      path="/main/resident-old/list"
                      component={ResidentOldListPage}
                    /> */}
                      {/* <Route
                      exact
                      path="/main/resident-old/detail/:id"
                      component={ResidentOldDetailPage}
                    /> */}
                      <Route
                        exact
                        path="/main/notification/list"
                        component={NotificationListPage}
                      />
                      <Route
                        exact
                        path="/main/notification/add/:id"
                        component={NotificationAddPage}
                      />
                      <Route
                        exact
                        path="/main/notification/edit/:id"
                        component={NotificationUpdatePage}
                      />
                      <Route
                        exact
                        path="/main/notification/detail/:id"
                        component={NotificationDetailPage}
                      />
                      <Route
                        exact
                        path="/main/notification"
                        render={() => <Redirect to="/main/notification/list" />}
                      />
                      {/* <Route
                      exact
                      path="/main/lucid/list"
                      component={LucidListPage}
                    />
                    <Route
                      exact
                      path="/main/lucid"
                      render={() => <Redirect to="/main/lucid/list" />}
                    /> */}
                      <Route
                        exact
                        path="/main/ticket/list"
                        component={TicketListPage}
                      />
                      <Route
                        exact
                        path="/main/ticket/detail/:id"
                        component={TicketDetailPage}
                      />
                      <Route
                        exact
                        path="/main/ticket"
                        render={() => <Redirect to="/main/ticket/list" />}
                      />
                      <Route
                        path="/main/service/detail/:suburl"
                        component={ServiceListPage}
                      />
                      <Route
                        path="/main/service/create/:suburl"
                        component={ServiceFeeCreatePage}
                      />
                      <Route
                        exact
                        path="/main/service/create"
                        render={() => (
                          <Redirect to="/main/service/create/generate" />
                        )}
                      />
                      <Route
                        exact
                        path="/main/service/add/:id"
                        component={ServiceAddPage}
                      />
                      <Route
                        exact
                        path="/main/service/providers/add"
                        component={ServiceProviderAddPage}
                      />
                      <Route
                        exact
                        path="/main/service/providers/detail/:id"
                        component={ServiceProviderDetailPage}
                      />
                      <Route
                        exact
                        path="/main/service/cloud"
                        component={ServiceCloudPage}
                      />
                      <Route
                        exact
                        path="/main/service/providers"
                        component={ServiceProviderPage}
                      />
                      <Route
                        exact
                        path="/main/finance/revenue-by-month"
                        component={RevenueByMonth}
                      />
                      <Route
                        exact
                        path="/main/finance/notification-fee/list"
                        component={NotificationFeeListPage}
                      />
                      <Route
                        exact
                        path="/main/finance/notification-fee/add"
                        component={NotificationFeePage}
                      />
                      <Route
                        exact
                        path="/main/finance/notification-fee/detail/:id"
                        component={NotificationFeeDetailPage}
                      />
                      <Route
                        exact
                        path="/main/finance/notification-fee/edit/:id"
                        component={NotificationFeeUpdatePage}
                      />
                      {/* <Route exact path="/main/finance/fees" component={FeeListPage} /> */}
                      <Route
                        exact
                        path="/main/finance/bills/detail/:id"
                        component={DashboardBillDetailPage}
                      />
                      <Route
                        exact
                        path="/main/finance/invoice-bills/detail/:id"
                        component={DashboardInvoiceBillDetailPage}
                      />
                      <Route
                        exact
                        path="/main/finance/bills"
                        component={BillListPage}
                      />
                      <Route
                        exact
                        path="/main/finance/bills-cancel"
                        component={CancelBillListPage}
                      />
                      <Route
                        exact
                        path="/main/finance/payment-request"
                        component={RequestPaymentPage}
                      />
                      <Route
                        exact
                        path={"/main/finance/payment-request/detail/:id"}
                        component={RequestPaymentDetail}
                      />
                      <Route
                        exact
                        path="/main/finance/debt"
                        component={DebtPage}
                      />
                      <Route
                        exact
                        path="/main/finance/debt-all"
                        component={DashboardDebtAllPage}
                      />
                      <Route
                        exact
                        path="/main/finance/cashbook"
                        component={CashBookPage}
                      />
                      <Route
                        exact
                        path="/main/finance/reception/bill/:id"
                        component={DashboardBillDetailPage}
                      />
                      <Route
                        exact
                        path="/main/finance/reception"
                        component={DashboardReceptionPage}
                      />
                      <Route
                        exact
                        path="/main/finance/invoice-bill"
                        component={DashboardInvoiceBillPage}
                      />
                      <Route
                        exact
                        path="/main/finance/invoice-bill/bill/:id"
                        component={DashboardInvoiceBillDetailPage}
                      />
                      <Route
                        exact
                        path="/main/finance/invoice-bills"
                        component={InvoiceBillListPage}
                      />
                      <Route
                        exact
                        path="/main/finance/invoice-bills-cancel"
                        component={CancelInvoiceBillListPage}
                      />
                      <Route
                        exact
                        path="/main/setting/building/infomation"
                        component={BuildingClusterInfomationPage}
                      />
                      <Route
                        exact
                        path="/main/setting/building/plane"
                        component={SettingPlanePage}
                      />
                      <Route
                        exact
                        path="/main/setting/plane"
                        component={SettingPlanePage}
                      />
                      <Route
                        exact
                        path="/main/setting/building/staff/list"
                        component={StaffListPage}
                      />
                      <Route
                        exact
                        path="/main/setting/building/staff/add"
                        render={() => <StaffAddPage key="addPage" />}
                      />
                      <Route
                        exact
                        path="/main/setting/building/staff/edit/:id"
                        render={() => <StaffAddPage key="editPage" />}
                      />
                      <Route
                        exact
                        path="/main/setting/building/staff/detail/:id"
                        render={() => <StaffDetailPage key="detailPage" />}
                      />
                      <Route
                        exact
                        path="/main/setting/building/staff"
                        render={() => (
                          <Redirect to="/main/setting/building/staff/list" />
                        )}
                      />
                      <Route
                        exact
                        path="/main/setting/notify/notify-send-config"
                        component={NotifySendConfigPage}
                      />
                      <Route
                        exact
                        path="/main/setting/notify/notify-receive-config"
                        component={NotifyReceiveConfigPage}
                      />
                      <Route
                        exact
                        path="/main/setting/notify/category"
                        component={NotificationCategoryPage}
                      />
                      <Route
                        exact
                        path="/main/setting/ticket/category"
                        component={TicketCategoryPage}
                      />
                      <Route
                        exact
                        path="/main/setting/roles/list"
                        component={RolesPage}
                      />
                      <Route
                        exact
                        path="/main/setting/roles/create"
                        render={() => <RolesSettingPage key="addRole" />}
                      />
                      <Route
                        exact
                        path="/main/setting/roles/edit/:id"
                        render={() => <RolesSettingPage key="editRole" />}
                      />
                      <Route
                        exact
                        path="/main/setting/roles/finance-notify"
                        component={SetupNotificationPage}
                      />
                      <Route
                        exact
                        path="/main/setting/logs"
                        component={ActionLogSystemPage}
                      />
                      <Route
                        exact
                        path="/main/setting/handbook"
                        component={ResidentHandbookPage}
                      />
                      <Route
                        exact
                        path="/main/setting/notification-fee-manager"
                        component={NotificationFeeManagerPage}
                      />
                      <Route
                        path="/main/setting/resources"
                        component={ResourceManagerPage}
                      />
                      {/* Setting Services */}
                      <Route
                        path="/main/setting/service/detail/:suburl"
                        component={SettingServiceListPage}
                      />
                      <Route
                        exact
                        path="/main/setting/service/create"
                        render={() => (
                          <Redirect to="/main/setting/service/create/generate" />
                        )}
                      />
                      <Route
                        exact
                        path="/main/setting/service/add/:id"
                        component={SettingServiceAddPage}
                      />
                      <Route
                        exact
                        path="/main/setting/service/cloud"
                        component={SettingServiceCloudPage}
                      />
                      <Route
                        exact
                        path="/main/setting"
                        render={() => (
                          <Redirect to="/main/setting/building/infomation" />
                        )}
                      />
                      <Route
                        exact
                        path="/main/setting/building"
                        render={() => (
                          <Redirect to="/main/setting/building/infomation" />
                        )}
                      />
                      <Route
                        exact
                        path="/main/setting/notify"
                        render={() => (
                          <Redirect to="/main/setting/notify/notify-send-config" />
                        )}
                      />
                      <Route
                        exact
                        path="/main/setting/roles"
                        render={() => (
                          <Redirect to="/main/setting/roles/list" />
                        )}
                      />
                      <Route
                        path="/main/history/carpacking"
                        component={HistoryCarpackingPage}
                      />
                      <Route
                        path="/main/history/accesscontrol"
                        component={HistoryAccessControlPage}
                      />
                      <Route
                        exact
                        path="/main/history"
                        render={() => (
                          <Redirect to="/main/history/carpacking" />
                        )}
                      />
                      <Route
                        path="/main/account/settings/:suburl"
                        component={AccountContainerPage}
                      />
                      <Route
                        exact
                        path="/main/account/settings"
                        render={() => (
                          <Redirect to="/main/account/settings/base" />
                        )}
                      />
                      <Route
                        exact
                        path="/main/service-utility-form/list"
                        component={FormList}
                      />
                      <Route
                        exact
                        path="/main/service-utility-form/detail/:id"
                        component={FormDetail}
                      />
                      <Route
                        exact
                        path="/main/service-utility-form/"
                        render={() => (
                          <Redirect to="/main/service-utility-form/list" />
                        )}
                      />
                      <Route
                        exact
                        path="/main/contractor/list"
                        component={ContractorPage}
                      />
                      <Route
                        exact
                        path="/main/contractor/detail/:id"
                        component={ContractorDetail}
                      />
                      <Route
                        exact
                        path="/main/contractor/add"
                        render={() => <ContractorAdd key={"addContractor"} />}
                      />
                      <Route
                        exact
                        path="/main/contractor/edit/:id"
                        render={() => <ContractorAdd key={"editContractor"} />}
                      />
                      <Route
                        exact
                        path="/main/contractor"
                        render={() => <Redirect to="/main/contractor/list" />}
                      />
                      <Route
                        exact
                        path="/main/maintain/list"
                        component={MaintainList}
                      />
                      <Route
                        exact
                        path="/main/maintain/"
                        render={() => <Redirect to="/main/maintain/list" />}
                      />
                      <Route
                        exact
                        path="/main/maintain/detail/:id"
                        render={() => <MaintainDetail />}
                      />
                      <Route
                        exact
                        path="/main/maintain/add"
                        render={() => <MaintainAdd key={"addDevice"} />}
                      />
                      <Route
                        exact
                        path="/main/maintain/edit/:id"
                        render={() => <MaintainAdd key={"editDevice"} />}
                      />
                      <Route
                        exact
                        path="/main/task/list"
                        component={TaskList}
                      />
                      <Route
                        exact
                        path="/main/task/detail/:id"
                        component={TaskDetail}
                      />
                      <Route exact path="/main/task/add" component={TaskAdd} />
                      <Route
                        exact
                        path="/main/task/edit/:id"
                        component={TaskEdit}
                      />
                      <Route
                        exact
                        path="/main/merge-card/list"
                        component={CombineCardList}
                      />
                      <Route
                        exact
                        path="/main/merge-card/detail/:id"
                        component={CombineCardDetail}
                      />
                      <Route
                        exact
                        path="/main/merge-card/detail/:id/active"
                        component={CombineCardActive}
                      />
                      <Route render={() => <Redirect to="/main/home" />} />
                    </Switch>
                  </MainLayout>
                );
              }}
            />
            <Route
              render={() => (
                <div
                  style={{
                    position: "absolute",
                    top: 0,
                    left: 0,
                    right: 0,
                    bottom: 0,
                  }}
                >
                  <NotFoundPage
                    redirect={currentToken ? "/main/home" : "/user/login"}
                  />
                </div>
              )}
            />
          </Switch>
          <GlobalStyle />
          <ModalImport />
        </div>
      </ConfigProvider>
    );
  }
}

const mapStateToProps = createStructuredSelector({
  isInited: selectInited(),
  currentToken: selectToken(),
  buildingCluster: selectBuildingCluster(),
  language: makeSelectLocale(),
});

export default withRouter(connect(mapStateToProps)(App));
