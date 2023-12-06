/**
 *
 * AccountContainer
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage } from "react-intl";
import { compose } from "redux";
import { Switch, Route, Redirect } from "react-router-dom";
import messages from "./messages";
import Page from "../../components/Page/Page";

import AccountBasePage from "./AccountBase";
import AccountSecurityPage from "./AccountSecurity";
import AccountChangePasswordPage from "./AccountChangePassword";
import AccountLanguage from "./AccountLanguage";

import styles from "./index.less";
import { Menu } from "antd";

/* eslint-disable react/prefer-stateless-function */
export class AccountContainer extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      mode: "inline",
    };
  }

  componentDidMount() {
    window.addEventListener("resize", this.resize);
    this.resize();
  }

  componentWillUnmount() {
    window.removeEventListener("resize", this.resize);
  }
  resize = () => {
    if (!this.main) {
      return;
    }
    const { mode: currentMode } = this.state;

    let mode = "inline";
    const { offsetWidth } = this.main;

    if (offsetWidth > 400 && offsetWidth < 641) {
      mode = "horizontal";
    }

    if (window.innerWidth < 768 && offsetWidth > 400) {
      mode = "horizontal";
    }

    if (mode !== currentMode) {
      requestAnimationFrame(() => this.setState({ mode }));
    }
  };

  selectKey = ({ key }) => {
    this.props.history.push(key);
  };
  render() {
    const { mode } = this.state;
    return (
      <Page inner className={styles.accountPage}>
        <div
          className={styles.main}
          ref={(ref) => {
            this.main = ref;
          }}
        >
          <div className={styles.leftmenu}>
            <Menu
              mode={mode}
              selectedKeys={[
                this.props.location.pathname.replace(
                  "/main/account/settings/changepassword"
                ),
                this.props.location.pathname.replace(
                  "/main/account/settings/language"
                ),
              ]}
              defaultSelectedKeys={["/main/account/settings/base"]}
              onClick={this.selectKey}
            >
              <Menu.Item key={"/main/account/settings/base"}>
                <FormattedMessage {...messages.information} />
              </Menu.Item>
              <Menu.Item key={"/main/account/settings/changepassword"}>
                <FormattedMessage {...messages.password} />
              </Menu.Item>
              <Menu.Item key={"/main/account/settings/language"}>
                <FormattedMessage {...messages.language} />
              </Menu.Item>
            </Menu>
          </div>
          <div className={styles.right}>
            <Switch>
              <Route
                path="/main/account/settings/base"
                component={AccountBasePage}
              />
              <Route
                path="/main/account/settings/changepassword"
                component={AccountChangePasswordPage}
              />
              {/* <Route
                path="/main/account/settings/security"
                component={AccountSecurityPage}
              /> */}
              <Route
                path="/main/account/settings/language"
                component={AccountLanguage}
              />
              <Route
                render={() => <Redirect to="/main/account/settings/base" />}
              />
            </Switch>
          </div>
        </div>
      </Page>
    );
  }
}

AccountContainer.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(null, mapDispatchToProps);

export default compose(withConnect)(AccountContainer);
