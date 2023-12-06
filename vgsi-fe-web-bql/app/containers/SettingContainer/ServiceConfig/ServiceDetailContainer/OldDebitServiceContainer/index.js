/**
 *
 * OldDebitServiceContainer
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../../../components/Page/Page";
import { Menu } from "antd";
import { Switch, Route, withRouter, Redirect } from "react-router-dom";

import InfomationPage from "./InfomationPage/Loadable";
import { defaultAction, fetchDetailService } from "./actions";
import makeSelectOldDebitServiceContainer from "./selectors";
import messages from "../../messages";
import { injectIntl } from "react-intl";

import("./index.less");

const KEY_MENU = {
  "/main/setting/service/detail/old_debit/infomation":
    "/main/setting/service/detail/old_debit/infomation",
};
/* eslint-disable react/prefer-stateless-function */
export class OldDebitServiceContainer extends React.PureComponent {
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
    const { oldDebitServiceContainer } = this.props;
    const { loading, data } = oldDebitServiceContainer;
    return (
      <Page className="OldDebitServiceContainer">
        {!(loading || !data) && (
          <div className="main">
            <Menu
              onClick={this.handleClick}
              selectedKeys={[KEY_MENU[this.props.location.pathname]]}
              className="leftmenu"
              mode="vertical-left"
            >
              <Menu.Item key="/main/setting/service/detail/old_debit/infomation">
                {this.props.intl.formatMessage(messages.information)}
              </Menu.Item>
            </Menu>
            <div className="right">
              <Switch>
                <Route
                  exact
                  path="/main/setting/service/detail/old_debit/infomation"
                  component={InfomationPage}
                />
                <Redirect to="/main/setting/service/detail/old_debit/infomation" />
              </Switch>
            </div>
          </div>
        )}
      </Page>
    );
  }
}

OldDebitServiceContainer.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  oldDebitServiceContainer: makeSelectOldDebitServiceContainer(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "oldDebitServiceContainer", reducer });
const withSaga = injectSaga({ key: "oldDebitServiceContainer", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(OldDebitServiceContainer)));
