/**
 *
 * ManagementClusterServiceContainer
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectManagementClusterServiceContainer from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../../components/Page/Page";
import { Col, Menu, Row, Tooltip } from "antd";
import { Switch, Route, withRouter, Redirect } from "react-router-dom";
import InfoUsagePage from "./InfoUsage/Loadable";
import PaymentPage from "./PaymentPage/Loadable";
import PaymentTemplatePage from "./PaymentTemplatePage/Loadable";
import { defaultAction, fetchDetailService } from "./actions";
import { FormattedMessage } from "react-intl";
import messages from "./messages";

import("./index.less");

const KEY_MENU = {
  "/main/service/detail/apartment-fee/usage":
    "/main/service/detail/apartment-fee/usage",
  "/main/service/detail/apartment-fee/payment":
    "/main/service/detail/apartment-fee/payment",
  "/main/service/detail/apartment-fee/payment-template":
    "/main/service/detail/apartment-fee/payment-template",
};
/* eslint-disable react/prefer-stateless-function */
export class ManagementClusterServiceContainer extends React.PureComponent {
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }
  componentDidMount() {
    this.props.dispatch(fetchDetailService(`/${this.props.base_url}`));
  }

  handleClick = (e) => {
    console.log("e.key", e.key);
    this.props.history.push(e.key);
  };

  render() {
    const { managementClusterServiceContainer } = this.props;
    const { loading, data } = managementClusterServiceContainer;
    return (
      <Page className="ManagementClusterServiceContainer">
        {!(loading || !data) && (
          <Row gutter={30} style={{ marginTop: 20 }}>
            <Col md={5} lg={4}>
              <Menu
                onClick={this.handleClick}
                selectedKeys={[KEY_MENU[this.props.location.pathname]]}
                mode="vertical-left"
                className="leftmenu"
              >
                <Menu.Item key="/main/service/detail/apartment-fee/usage">
                  <Tooltip title={<FormattedMessage {...messages.usage} />}>
                    <FormattedMessage {...messages.usage} />
                  </Tooltip>
                </Menu.Item>
                <Menu.Item key="/main/service/detail/apartment-fee/payment">
                  <Tooltip
                    title={<FormattedMessage {...messages.approvedFee} />}
                  >
                    <FormattedMessage {...messages.approvedFee} />
                  </Tooltip>
                </Menu.Item>
                <Menu.Item key="/main/service/detail/apartment-fee/payment-template">
                  <Tooltip
                    title={<FormattedMessage {...messages.waitApproveFee} />}
                  >
                    <FormattedMessage {...messages.waitApproveFee} />
                  </Tooltip>
                </Menu.Item>
              </Menu>
            </Col>
            <Col md={19} lg={20}>
              <Switch>
                <Route
                  exact
                  path="/main/service/detail/apartment-fee/usage"
                  component={InfoUsagePage}
                />
                <Route
                  exact
                  path="/main/service/detail/apartment-fee/payment"
                  component={PaymentPage}
                />
                <Route
                  exact
                  path="/main/service/detail/apartment-fee/payment-template"
                  component={PaymentTemplatePage}
                />
                <Redirect to="/main/service/detail/apartment-fee/usage" />
              </Switch>
            </Col>
          </Row>
        )}
      </Page>
    );
  }
}

ManagementClusterServiceContainer.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  managementClusterServiceContainer:
    makeSelectManagementClusterServiceContainer(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({
  key: "managementClusterServiceContainer",
  reducer,
});
const withSaga = injectSaga({ key: "managementClusterServiceContainer", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(ManagementClusterServiceContainer));
