/**
 *
 * ServiceList
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectServiceList from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../../components/Page/Page";
import { Button, Menu, Result } from "antd";
import { defaultActionList, fetchAllServiceList } from "./actions";
import { Switch, Route, Redirect } from "react-router-dom";
import WaterServiceContainerPage from "../ServiceDetailContainer/WaterServiceContainer/Loadable";
import ElectricServiceContainerPage from "../ServiceDetailContainer/ElectricServiceContainer/Loadable";
import MotoPackingServiceContainerPage from "../ServiceDetailContainer/MotoPackingServiceContainer/Loadable";
import ManagementClusterServiceContainerPage from "../ServiceDetailContainer/ManagementClusterServiceContainer/Loadable";
import UtilityFreeServiceContainerPage from "../ServiceDetailContainer/UtilityFreeServiceContainer/Loadable";
import OldDebitServiceContainerPage from "../ServiceDetailContainer/OldDebitServiceContainer/Loadable";
import messages from "../messages";
import { injectIntl } from "react-intl";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import("./index.less");

/* eslint-disable react/prefer-stateless-function */
export class ServiceList extends React.PureComponent {
  componentWillUnmount() {
    this.props.dispatch(defaultActionList());
  }

  componentDidMount() {
    this.props.dispatch(fetchAllServiceList());
  }

  selectKey = ({ key }) => {
    this.props.history.push(`/main/setting/service/detail${key}`);
  };
  render() {
    const { serviceList, language } = this.props;
    const { loading, items } = serviceList;
    const { suburl } = this.props.match.params;
    let currentService =
      items &&
      items.find((item) =>
        this.props.location.pathname.includes(item.service_base_url)
      );
    let domain = items.length > 0 ? items[0].service_base_url : "";
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Page inner className={"serviceListPage"} loading={loading}>
        {!loading && items.length > 0 ? (
          <div className={"main"}>
            <div className={"right"}>
              <Menu
                mode={"horizontal"}
                selectedKeys={[
                  currentService === undefined
                    ? domain
                    : currentService && currentService.service_base_url,
                ]}
                onClick={this.selectKey}
              >
                {items.map((item) => {
                  return (
                    <Menu.Item key={item.service_base_url}>
                      {language === "en"
                        ? item.service_name_en
                        : item.service_name}
                    </Menu.Item>
                  );
                })}
              </Menu>
              <Switch>
                <Route
                  path="/main/setting/service/detail/water/:sub"
                  render={(props) => (
                    <WaterServiceContainerPage {...props} base_url={suburl} />
                  )}
                />
                <Route
                  exact
                  path="/main/setting/service/detail/water"
                  render={() => (
                    <Redirect to="/main/setting/service/detail/water/infomation" />
                  )}
                />

                <Route
                  path="/main/setting/service/detail/electric/:sub"
                  render={(props) => (
                    <ElectricServiceContainerPage
                      {...props}
                      base_url={suburl}
                    />
                  )}
                />
                <Route
                  exact
                  path="/main/setting/service/detail/electric"
                  render={() => (
                    <Redirect to="/main/setting/service/detail/electric/infomation" />
                  )}
                />

                <Route
                  path="/main/setting/service/detail/moto-packing/:sub"
                  render={(props) => (
                    <MotoPackingServiceContainerPage
                      {...props}
                      base_url={suburl}
                    />
                  )}
                />
                <Route
                  exact
                  path="/main/setting/service/detail/moto-packing"
                  render={() => (
                    <Redirect to="/main/setting/service/detail/moto-packing/infomation" />
                  )}
                />

                <Route
                  path="/main/setting/service/detail/apartment-fee/:sub"
                  render={(props) => (
                    <ManagementClusterServiceContainerPage
                      {...props}
                      base_url={suburl}
                    />
                  )}
                />
                <Route
                  exact
                  path="/main/setting/service/detail/apartment-fee"
                  render={() => (
                    <Redirect to="/main/setting/service/detail/apartment-fee/infomation" />
                  )}
                />

                <Route
                  path="/main/setting/service/detail/utility-free/:sub"
                  render={(props) => (
                    <UtilityFreeServiceContainerPage
                      {...props}
                      base_url={suburl}
                    />
                  )}
                />
                <Route
                  exact
                  path="/main/setting/service/detail/utility-free"
                  render={() => (
                    <Redirect to="/main/setting/service/detail/utility-free/infomation" />
                  )}
                />

                <Route
                  path="/main/setting/service/detail/old_debit/:sub"
                  render={(props) => (
                    <OldDebitServiceContainerPage
                      {...props}
                      base_url={suburl}
                    />
                  )}
                />
                <Route
                  exact
                  path="/main/setting/service/detail/old_debit"
                  render={() => (
                    <Redirect to="/main/setting/service/detail/old_debit/infomation" />
                  )}
                />

                <Redirect to={`/main/setting/service/detail${domain}`} />
              </Switch>
            </div>
          </div>
        ) : (
          <Result
            status="404"
            // title="Kh"
            subTitle={formatMessage(messages.notFoundService)}
            extra={
              <Button
                type="primary"
                onClick={() => this.props.history.push("/main/service/cloud")}
              >
                {formatMessage(messages.addService)}
              </Button>
            }
          />
        )}
      </Page>
    );
  }
}

ServiceList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  serviceList: makeSelectServiceList(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "serviceListCustom", reducer });
const withSaga = injectSaga({ key: "serviceListCustom", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ServiceList));
