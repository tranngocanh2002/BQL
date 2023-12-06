import React, { PureComponent, Fragment } from "react";
import PropTypes from "prop-types";
import { Menu, Icon, Row } from "antd";

import { withRouter } from "react-router";
import { Link } from "react-router-dom";
import {
  arrayToTree,
  queryAncestors,
  pathMatchRegexp,
  addLangPrefix,
} from "../../utils";
import { createStructuredSelector } from "reselect";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { connect } from "react-redux";

const { SubMenu } = Menu;

class SiderMenu extends PureComponent {
  state = {
    openKeys: [],
  };

  onOpenChange = (openKeys) => {
    const { menus } = this.props;
    const rootSubmenuKeys = menus
      .filter((_) => !_.menuParentId)
      .map((_) => _.id);

    const latestOpenKey = openKeys.find(
      (key) => this.state.openKeys.indexOf(key) === -1
    );

    let newOpenKeys = openKeys;
    if (rootSubmenuKeys.indexOf(latestOpenKey) !== -1) {
      newOpenKeys = latestOpenKey ? [latestOpenKey] : [];
    }

    this.setState({
      openKeys: newOpenKeys,
    });
  };

  generateMenus = (data, level = 0) =>
    data.map((item) => {
      if (item.invisibleInMenu) {
        return;
      }
      if (item.children && item.children.length > 0) {
        return (
          <SubMenu
            key={item.id}
            title={
              <Fragment>
                {item.icon &&
                  (typeof item.icon == "string" ? (
                    <Icon type={item.icon} />
                  ) : (
                    <i className={item.icon.className} style={{ fontSize: 16 }}>
                      {item.icon.name}
                    </i>
                  ))}
                <span>
                  {this.props.language == "vi"
                    ? item.name
                    : item.name_en
                    ? item.name_en
                    : item.name}
                </span>
              </Fragment>
            }
          >
            {this.generateMenus(item.children, level + 1)}
          </SubMenu>
        );
      }
      return (
        <Menu.Item key={item.id} style={{ padding: "0 16px" }}>
          <Link to={addLangPrefix(item.route) || "#"}>
            <Row type="flex" align="middle">
              {item.icon &&
                level !== 2 &&
                (typeof item.icon == "string" ? (
                  <Icon type={item.icon} />
                ) : (
                  <i className={item.icon.className} style={{ fontSize: 16 }}>
                    {item.icon.name}
                  </i>
                ))}
              <span
                style={
                  level != 0 && this.props.collapsed ? { marginLeft: 0 } : {}
                }
              >
                {this.props.language == "vi"
                  ? item.name
                  : item.name_en
                  ? item.name_en
                  : item.name}
              </span>
            </Row>
          </Link>
        </Menu.Item>
      );
    });

  render() {
    const { collapsed, theme, menus, location, isMobile, onCollapseChange } =
      this.props;
    // Generating tree-structured data for menu content.
    const menuTree = arrayToTree(menus, "id", "menuParentId");
    let menuActive = null;
    // Find a menu that matches the pathname.
    const currentMenu = menus.find((_) => {
      if (_.children) {
        menuActive = _.children.find((item) => {
          return item.route && pathMatchRegexp(item.route, location.pathname);
        });
        return menuActive;
      } else {
        return _.route && pathMatchRegexp(_.route, location.pathname);
      }
    });
    // Find the key that should be selected according to the current menu.
    let selectedKeys = currentMenu
      ? queryAncestors(
          menus,
          currentMenu,
          currentMenu.invisibleInMenu ? "breadcrumbParentId" : "menuParentId"
        ).map((_) => _.id)
      : [];

    if (menuActive) {
      selectedKeys = [...selectedKeys, menuActive.id];
    }
    const menuProps = collapsed
      ? {}
      : {
          openKeys: this.state.openKeys,
        };

    return (
      <Menu
        mode="inline"
        theme={theme}
        onOpenChange={this.onOpenChange}
        inlineCollapsed={collapsed}
        selectedKeys={selectedKeys}
        onClick={
          isMobile
            ? () => {
                onCollapseChange(true);
              }
            : undefined
        }
        {...menuProps}
      >
        {this.generateMenus(menuTree)}
      </Menu>
    );
  }
}

SiderMenu.propTypes = {
  menus: PropTypes.array,
  theme: PropTypes.string,
  isMobile: PropTypes.bool,
  collapsed: PropTypes.bool,
  onCollapseChange: PropTypes.func,
};

const mapStateToProps = createStructuredSelector({
  language: makeSelectLocale(),
});

export default withRouter(connect(mapStateToProps)(SiderMenu));
