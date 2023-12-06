/**
 *
 * ServiceList
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "./messages";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectServiceList from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../components/Page/Page";
import { Button, Col, Menu, Result, Row } from "antd";
import { defaultAction, fetchAllService } from "./actions";
import { Switch, Route, Redirect } from "react-router-dom";
import WaterServiceContainerPage from "../ServiceDetailContainer/WaterServiceContainer/Loadable";
import ElectricServiceContainerPage from "../ServiceDetailContainer/ElectricServiceContainer/Loadable";
import MotoPackingServiceContainerPage from "../ServiceDetailContainer/MotoPackingServiceContainer/Loadable";
import ManagementClusterServiceContainerPage from "../ServiceDetailContainer/ManagementClusterServiceContainer/Loadable";
import UtilityFreeServiceContainerPage from "../ServiceDetailContainer/UtilityFreeServiceContainer/Loadable";
import OldDebitServiceContainerPage from "../ServiceDetailContainer/OldDebitServiceContainer/Loadable";
import FeeList from "../../FinanceContainer/FeeList/Loadable";
import { selectAuthGroup } from "../../../redux/selectors";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

import("./index.less");

/* eslint-disable react/prefer-stateless-function */
export class ServiceList extends React.PureComponent {
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchAllService());
  }

  selectKey = ({ key }) => {
    this.props.history.push(`/main/service/detail${key}`);
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
    return (
      <Page inner className={"serviceListPage"} loading={loading}>
        {!loading && items.length == 0 ? (
          <Result
            status="404"
            // title="Kh"
            subTitle={<FormattedMessage {...messages.empty} />}
            extra={
              <Button
                type="primary"
                onClick={() => this.props.history.push("/main/service/cloud")}
              >
                <FormattedMessage {...messages.add} />
              </Button>
            }
          />
        ) : (
          <Row>
            <Col span={24}>
              <Menu
                mode={"horizontal"}
                selectedKeys={[
                  currentService ? currentService.service_base_url : "/fees",
                ]}
                onClick={this.selectKey}
              >
                <Menu.Item key="/fees">
                  <FormattedMessage {...messages.feeList} />
                </Menu.Item>
                {items.map((item) => {
                  return (
                    <Menu.Item key={item.service_base_url}>
                      {language == "en"
                        ? item.service_name_en
                        : item.service_name}
                    </Menu.Item>
                  );
                })}
              </Menu>
              <Switch>
                <Route
                  path="/main/service/detail/fees"
                  render={(props) => <FeeList {...props} />}
                />
                <Route
                  path="/main/service/detail/water/:sub"
                  render={(props) => (
                    <WaterServiceContainerPage {...props} base_url={suburl} />
                  )}
                />
                <Route
                  exact
                  path="/main/service/detail/water"
                  render={() => (
                    <Redirect to="/main/service/detail/water/usage" />
                  )}
                />

                <Route
                  path="/main/service/detail/electric/:sub"
                  render={(props) => (
                    <ElectricServiceContainerPage
                      {...props}
                      base_url={suburl}
                    />
                  )}
                />
                <Route
                  exact
                  path="/main/service/detail/electric"
                  render={() => (
                    <Redirect to="/main/service/detail/electric/usage" />
                  )}
                />

                <Route
                  path="/main/service/detail/moto-packing/:sub"
                  render={(props) => (
                    <MotoPackingServiceContainerPage
                      {...props}
                      base_url={suburl}
                    />
                  )}
                />
                <Route
                  exact
                  path="/main/service/detail/moto-packing"
                  render={() => (
                    <Redirect to="/main/service/detail/moto-packing/vehicle" />
                  )}
                />

                <Route
                  path="/main/service/detail/apartment-fee/:sub"
                  render={(props) => (
                    <ManagementClusterServiceContainerPage
                      {...props}
                      base_url={suburl}
                    />
                  )}
                />
                <Route
                  exact
                  path="/main/service/detail/apartment-fee"
                  render={() => (
                    <Redirect to="/main/service/detail/apartment-fee/usage" />
                  )}
                />

                <Route
                  path="/main/service/detail/utility-free/:sub"
                  render={(props) => (
                    <UtilityFreeServiceContainerPage
                      {...props}
                      base_url={suburl}
                    />
                  )}
                />
                <Route
                  exact
                  path="/main/service/detail/utility-free"
                  render={() => (
                    <Redirect to="/main/service/detail/utility-free/booking-fee-list" />
                  )}
                />

                <Route
                  path="/main/service/detail/old_debit/:sub"
                  render={(props) => (
                    <OldDebitServiceContainerPage
                      {...props}
                      base_url={suburl}
                    />
                  )}
                />
                <Route
                  exact
                  path="/main/service/detail/old_debit"
                  render={() => (
                    <Redirect to="/main/service/detail/old_debit/lock" />
                  )}
                />

                <Redirect to={"/main/service/detail/fees"} />
              </Switch>
            </Col>
          </Row>
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
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "serviceList", reducer });
const withSaga = injectSaga({ key: "serviceList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ServiceList));
