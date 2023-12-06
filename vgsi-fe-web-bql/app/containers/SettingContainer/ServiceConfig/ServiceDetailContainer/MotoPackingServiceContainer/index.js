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
import Page from "../../../../../components/Page/Page";
import { Menu } from "antd";
import { Switch, Route, withRouter, Redirect } from "react-router-dom";
import InfomationPage from "./InfomationPage/Loadable";
import SetupFeePage from "./SetupFee/Loadable";
import { defaultAction, fetchDetailService } from "./actions";
import("./index.less");
import { injectIntl } from "react-intl";
import messages from "../../messages";

const KEY_MENU = {
  "/main/setting/service/detail/moto-packing/infomation":
    "/main/setting/service/detail/moto-packing/infomation",
  "/main/setting/service/detail/moto-packing/setup-fee":
    "/main/setting/service/detail/moto-packing/setup-fee",
};

/* eslint-disable react/prefer-stateless-function */
export class MotoPackingServiceContainer extends React.PureComponent {
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }
  componentDidMount() {
    console.log(this.props);
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
        {/* loading={loading || !!!data} > */}
        {!(loading || !data) && (
          <div className="main">
            <Menu
              onClick={this.handleClick}
              selectedKeys={[KEY_MENU[this.props.location.pathname]]}
              mode="vertical-left"
              className="leftmenu"
            >
              <Menu.Item key="/main/setting/service/detail/moto-packing/infomation">
                {this.props.intl.formatMessage(messages.information)}
              </Menu.Item>
              <Menu.Item key="/main/setting/service/detail/moto-packing/setup-fee">
                {this.props.intl.formatMessage(messages.settingFee)}
              </Menu.Item>
            </Menu>
            <div className="right">
              <Switch>
                <Route
                  exact
                  path="/main/setting/service/detail/moto-packing/infomation"
                  component={InfomationPage}
                />
                <Route
                  exact
                  path="/main/setting/service/detail/moto-packing/setup-fee"
                  component={SetupFeePage}
                />
                <Redirect to="/main/setting/service/detail/moto-packing/infomation" />
              </Switch>
            </div>
          </div>
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
