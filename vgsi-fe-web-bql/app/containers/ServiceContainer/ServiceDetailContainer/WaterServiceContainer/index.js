/**
 *
 * WaterServiceContainer
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";
import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectWaterServiceContainer from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../../components/Page/Page";
import { Col, Menu, Row, Tooltip } from "antd";

import { Switch, Route, withRouter, Redirect } from "react-router-dom";
import InfoUsagePage from "./InfoUsage/Loadable";
import LockFeePage from "./LockFeePage/Loadable";
import LockFeeTemplatePage from "./LockFeeTemplatePage/Loadable";
import { defaultAction, fetchDetailService } from "./actions";
import { FormattedMessage } from "react-intl";
import messages from "./messages";

import("./index.less");

const KEY_MENU = {
  "/main/service/detail/water/usage": "/main/service/detail/water/usage",
  "/main/service/detail/water/lock": "/main/service/detail/water/lock",
  "/main/service/detail/water/lock-template":
    "/main/service/detail/water/lock-template",
};
/* eslint-disable react/prefer-stateless-function */
export class WaterServiceContainer extends React.PureComponent {
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
    const { waterServiceContainer } = this.props;
    const { loading, data } = waterServiceContainer;
    return (
      // ={loading || !!!data}
      <Page className="WaterServiceContainer">
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
                className="leftmenu"
                mode="vertical-left"
              >
                <Menu.Item key="/main/service/detail/water/usage">
                  <Tooltip title="Sử dụng">
                    <FormattedMessage {...messages.usage} />
                  </Tooltip>
                </Menu.Item>
                <Menu.Item key="/main/service/detail/water/lock">
                  <Tooltip title="Số nước đã duyệt">
                    <FormattedMessage {...messages.approvedWater} />
                  </Tooltip>
                </Menu.Item>
                <Menu.Item key="/main/service/detail/water/lock-template">
                  <Tooltip title="Số nước chưa duyệt">
                    <FormattedMessage {...messages.waitingApproveWater} />
                  </Tooltip>
                </Menu.Item>
              </Menu>
            </Col>

            <Col md={19} lg={20}>
              <Switch>
                <Route
                  exact
                  path="/main/service/detail/water/usage"
                  component={InfoUsagePage}
                />
                <Route
                  exact
                  path="/main/service/detail/water/lock"
                  component={LockFeePage}
                />
                <Route
                  exact
                  path="/main/service/detail/water/lock-template"
                  component={LockFeeTemplatePage}
                />
                <Redirect to="/main/service/detail/water/usage" />
              </Switch>
            </Col>
          </Row>
        )}
      </Page>
    );
  }
}

WaterServiceContainer.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  waterServiceContainer: makeSelectWaterServiceContainer(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "waterServiceContainer", reducer });
const withSaga = injectSaga({ key: "waterServiceContainer", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(WaterServiceContainer));
