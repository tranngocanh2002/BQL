/**
 *
 * MotoPackingServiceContainer
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectMotoPackingServiceContainer from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../../components/Page/Page";
import { Col, Menu, Row } from "antd";
import { Switch, Route, withRouter, Redirect } from "react-router-dom";

import LockFeeTemplatePage from "./LockFeeTemplatePage/Loadable";
import LockFeePage from "./LockFeePage/Loadable";
import VehicleManagementPage from "./VehicleManagement/Loadable";
import { defaultAction, fetchDetailService } from "./actions";
import("./index.less");
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "./messages";

const KEY_MENU = {
  "/main/service/detail/moto-packing/payment":
    "/main/service/detail/moto-packing/payment",
  "/main/service/detail/moto-packing/payment-template":
    "/main/service/detail/moto-packing/payment",
  "/main/service/detail/moto-packing/vehicle":
    "/main/service/detail/moto-packing/vehicle",
  "/main/service/detail/moto-packing/lock-template":
    "/main/service/detail/moto-packing/lock-template",
  "/main/service/detail/moto-packing/lock":
    "/main/service/detail/moto-packing/lock",
};

/* eslint-disable react/prefer-stateless-function */
export class MotoPackingServiceContainer extends React.PureComponent {
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }
  componentDidMount() {
    this.props.dispatch(fetchDetailService(`/${this.props.base_url}`));
  }

  handleClick = (e) => {
    this.props.history.push(e.key);
  };

  render() {
    const { motoPackingServiceContainer } = this.props;
    const { loading, data } = motoPackingServiceContainer;
    return (
      <Page className="MotoPackingServiceContainer">
        {!(loading || !data) && (
          <Row
            gutter={30}
            style={{
              marginTop: 20,
            }}
          >
            <Col md={5} lg={4}>
              <Menu
                onClick={this.handleClick}
                selectedKeys={[KEY_MENU[this.props.location.pathname]]}
                mode="vertical-left"
                className="leftmenu"
              >
                <Menu.Item key="/main/service/detail/moto-packing/vehicle">
                  <FormattedMessage {...messages.manageVehicle} />
                </Menu.Item>
                <Menu.Item key="/main/service/detail/moto-packing/lock">
                  <FormattedMessage {...messages.approvedFeeVehicle} />
                </Menu.Item>
                <Menu.Item key="/main/service/detail/moto-packing/lock-template">
                  <FormattedMessage {...messages.waitApproveFeeVehicle} />
                </Menu.Item>
              </Menu>
            </Col>

            <Col md={19} lg={20}>
              <Switch>
                <Route
                  exact
                  path="/main/service/detail/moto-packing/lock"
                  component={LockFeePage}
                />
                <Route
                  exact
                  path="/main/service/detail/moto-packing/lock-template"
                  component={LockFeeTemplatePage}
                />
                <Route
                  exact
                  path="/main/service/detail/moto-packing/vehicle"
                  component={VehicleManagementPage}
                />
                <Redirect to="/main/service/detail/moto-packing/vehicle" />
              </Switch>
            </Col>
          </Row>
        )}
      </Page>
    );
  }
}

MotoPackingServiceContainer.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  motoPackingServiceContainer: makeSelectMotoPackingServiceContainer(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({
  key: "motoPackingServiceContainer",
  reducer,
});
const withSaga = injectSaga({ key: "motoPackingServiceContainer", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(MotoPackingServiceContainer)));
