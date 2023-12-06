/**
 *
 * MainLayout
 *
 */

import PropTypes from "prop-types";
import React, { Fragment } from "react";
import { connect } from "react-redux";
import { compose } from "redux";

import { Loader } from "components";
import { Helmet } from "react-helmet";

import { BackTop, Drawer, Layout } from "antd";
import { Bread, Header, Sider } from "../../components/Layout";

import { arrayToTree, config, pathMatchRegexp } from "../../utils";
import "./BaseLayout.less";
import styles from "./PrimaryLayout.less";
// const { Header, Bread, Sider } = MyLayout

import { enquireScreen, unenquireScreen } from "enquire-js";
import { Redirect } from "react-router";
import { createStructuredSelector } from "reselect";
import { clearAllNotification, logout } from "../../redux/actions/config";
import { selectAuthGroup, selectToken } from "../../redux/selectors";
import PermissionDeniedPage from "../PermissionDeniedPage";

const { Content } = Layout;

// import NProgress from 'nprogress'

/* eslint-disable react/prefer-stateless-function */
export class MainLayout extends React.PureComponent {
  constructor(props) {
    super(props);

    let routers = this.remainRouter(config.ROUTERS, props.auth_group);

    this.state = {
      isMobile: false,
      collapsed: false,
      theme: "dark",
      routers,
    };
  }

  remainRouter(routers, auth_group) {
    if (!auth_group || !auth_group.data_role) return routers;
    const menuTree = arrayToTree(routers, "id", "menuParentId");
    const { data_web } = auth_group;
    let newData_web = Object.values(data_web);
    let newRouter = [];
    menuTree.forEach((root) => {
      const { children, ...rest } = root;
      if (root.main) {
        newRouter.push(rest);
        if (children) {
          newRouter = newRouter.concat(children);
        }
      } else {
        if (children) {
          let hasChild = false;
          children.forEach((child) => {
            const { children, ...rest } = child;
            if (children) {
              let hasChild2 = false;
              children.forEach((child2) => {
                if (
                  child2.main ||
                  newData_web.some((rr) => rr == child2.route)
                ) {
                  hasChild2 = true;
                  hasChild = true;
                  newRouter.push({
                    ...child2,
                    denied: false,
                  });
                }
              });
              if (hasChild2) {
                hasChild = true;
                newRouter.push({
                  ...rest,
                  denied: false,
                });
              }
            } else {
              if (child.main || newData_web.some((rr) => rr == child.route)) {
                hasChild = true;
                newRouter.push({
                  ...child,
                  denied: false,
                });
              }
            }
          });
          if (hasChild) {
            newRouter.push({
              ...rest,
              denied: false,
            });
          }
        } else {
          if (rest.main || newData_web.some((rr) => rr == rest.route)) {
            newRouter.push({
              ...rest,
              denied: false,
            });
          }
        }
      }
    });
    return newRouter;
  }

  componentWillReceiveProps(nextProps) {
    if (this.props.auth_group != nextProps.auth_group) {
      this.setState({
        routers: this.remainRouter(config.ROUTERS, nextProps.auth_group),
      });
    }
  }

  componentDidMount() {
    this.enquireHandler = enquireScreen((mobile) => {
      const { isMobile } = this.state;
      if (isMobile !== mobile) {
        this.setState({
          isMobile: mobile,
        });
      }
    });
  }

  componentWillUnmount() {
    unenquireScreen(this.enquireHandler);
  }

  onCollapseChange = (collapsed) => {
    this.setState({
      collapsed,
    });
  };

  onThemeChange = (theme) => {
    this.setState({
      theme,
    });
  };

  render() {
    const { isMobile, collapsed, theme, routers } = this.state;
    const { children, tokenCurrent, dispatch } = this.props;
    if (!tokenCurrent) {
      return <Redirect to="/user/login" />;
    }
    const hasPermission = (
      routers.find((_) => {
        if (_.children) {
          let a = _.children.find((item) => {
            return item.route && pathMatchRegexp(item.route, location.pathname);
          });
          return a;
        } else {
          return _.route && pathMatchRegexp(_.route, location.pathname);
        }
      }) || { denied: true }
    ).denied;
    const menus = routers.filter((_) => _.menuParentId !== "-1" && !_.denied);
    // console.log("menus", menus);
    // console.log(
    //   "arrayToTree",
    //   arrayToTree(config.ROUTERS, "id", "menuParentId")
    // );
    // console.log("routers2", routers);
    const headerProps = {
      menus,
      collapsed,
      isMobile,
      onCollapseChange: this.onCollapseChange,
      fixed: false,
      onAllNotificationsRead() {
        dispatch(clearAllNotification());
      },
      onSignOut() {
        dispatch(logout());
      },
    };

    const siderProps = {
      theme,
      menus,
      isMobile,
      collapsed,
      onCollapseChange: this.onCollapseChange,
      onThemeChange: this.onThemeChange,
    };
    // NProgress.start()
    return (
      <Fragment>
        <Helmet>
          <title>{config.siteName}</title>
        </Helmet>
        <Loader fullScreen spinning={false} />
        <Fragment>
          <Layout>
            {isMobile ? (
              <Drawer
                maskClosable
                closable={false}
                onClose={this.onCollapseChange.bind(this, !collapsed)}
                visible={!collapsed}
                placement="left"
                width={256}
                style={{
                  padding: 0,
                  height: "100vh",
                }}
              >
                <Sider {...siderProps} collapsed={false} />
              </Drawer>
            ) : (
              <Sider {...siderProps} />
            )}
            <div
              className={styles.container}
              style={{
                width: window.innerWidth - 256,
                paddingTop: headerProps.fixed ? 72 : 0,
              }}
              id="primaryLayout"
            >
              <Header {...headerProps} />
              <Bread collapsed={collapsed} routeList={routers} />
              <Content style={{ padding: 24 }} className={styles.content}>
                {!hasPermission || routers.map((e, index) => index > 108) ? (
                  children
                ) : (
                  <PermissionDeniedPage />
                )}
              </Content>
              <BackTop
                className={styles.backTop}
                target={() => document.querySelector("#primaryLayout")}
              />
              {/* <GlobalFooter
							 className={styles.footer}
							 copyright={config.copyright}
							 /> */}
            </div>
          </Layout>
        </Fragment>
      </Fragment>
    );
  }
}

MainLayout.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  tokenCurrent: selectToken(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

export default compose(withConnect)(MainLayout);
