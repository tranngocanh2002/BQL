/**
 *
 * ResidentHandbook
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Icon, Modal, Row, Table, Tooltip, Typography } from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page";
import messages from "../messages";
import {
  addCategory,
  addHandbook,
  deleteCategory,
  deleteHandbook,
  editCategory,
  editHandbook,
  fetchCategory,
  fetchHandbook,
} from "./actions";
import styles from "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectResidentHandbook from "./selectors";

import { selectAuthGroup } from "../../../redux/selectors";
import ModalCreateCategory from "./ModalCreateCategory";
import ModalCreateHandbookItem from "./ModalCreateHandbookItem";

import WithRole from "../../../components/WithRole";
import { config } from "../../../utils";

import { ContentState, EditorState, convertToRaw } from "draft-js";
import htmlToDraft from "html-to-draftjs";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

/* eslint-disable react/prefer-stateless-function */
export class ResidentHandbook extends React.PureComponent {
  state = {
    visibleModalCategory: false,
    visibleModalItem: false,
    currentCategory: undefined,
  };
  reload = () => {
    this.setState(
      {
        visibleModalCategory: false,
        visibleModalItem: false,
        currentCategory: undefined,
      },
      () => {
        this.props.dispatch(fetchCategory());
      }
    );

    this.props.dispatch(fetchHandbook());
  };
  componentDidMount() {
    this.props.dispatch(fetchCategory());
    this.props.dispatch(fetchHandbook());
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.residentHandbook.category.addSuccess !=
        nextProps.residentHandbook.category.addSuccess &&
      nextProps.residentHandbook.category.addSuccess
    ) {
      this.setState({
        visibleModalCategory: false,
        currentCategory: undefined,
      });
    }
    if (
      this.props.residentHandbook.handbook.addSuccess !=
        nextProps.residentHandbook.handbook.addSuccess &&
      nextProps.residentHandbook.handbook.addSuccess
    ) {
      this.setState({
        visibleModalItem: false,
        currentItem: undefined,
      });
    }
  }

  _onEditCategory = (record) => {
    this.setState({
      currentCategory: record,
      visibleModalCategory: true,
    });
  };

  _onDeleteCategory = (record) => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmDeleteCategory),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      width: 450,
      onOk: () => {
        this.props.dispatch(
          deleteCategory({
            // apartment_id: record.apartment_id,
            id: record.id,
          })
        );
      },
      onCancel() {},
    });
  };

  render() {
    const { language } = this.props;
    const { category, handbook } = this.props.residentHandbook;
    const formatMessage = this.props.intl.formatMessage;
    const columns = [
      {
        width: 50,
        align: "center",
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record) => <span>{record.order}</span>,
      },
      {
        // align: 'center',
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.nameCategory)}
          </span>
        ),
        dataIndex: language === "en" ? "name_en" : "name",
        key: "name",
      },
      {
        width: 200,
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.action)}
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record) => (
          <Row>
            <Tooltip title={formatMessage(messages.edit)}>
              <i
                className="fa fa-edit"
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  this._onEditCategory(record);
                }}
              />
            </Tooltip>
            &ensp;&ensp;|&ensp;&ensp;
            <Tooltip title={formatMessage(messages.add)}>
              <Icon
                className={styles.iconAction}
                style={{ color: "#1997FC" }}
                type="plus"
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  // this._onEditCategory(record)
                  this.setState({
                    visibleModalItem: true,
                    currentCategory: record.id,
                  });
                }}
              />
            </Tooltip>
            &ensp;&ensp;|&ensp;&ensp;
            <Tooltip title={formatMessage(messages.deleteCategory)}>
              {" "}
              <i
                className="fa fa-trash"
                style={{ color: "red" }}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  this._onDeleteCategory(record);
                }}
              />
            </Tooltip>
          </Row>
        ),
      },
    ];
    const columnsExpand = [
      // {
      //   width: 50,
      //   align: 'center',
      //   title: <span className={styles.nameTable} >#</span>, dataIndex: 'id', key: 'id',
      //   render: (text, record, index) => <span >{index + 1}</span>
      // },
      {
        width: 200,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.title)}
          </span>
        ),
        dataIndex: language === "en" ? "title_en" : "title",
        key: "title",
      },
      {
        // align: 'center',
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.content)}
          </span>
        ),
        dataIndex: "content",
        key: "content",
        render: (text) => {
          try {
            let blockArray = htmlToDraft(text);
            let editorState = EditorState.createWithContent(
              ContentState.createFromBlockArray(blockArray.contentBlocks)
            );
            let contentRaw = convertToRaw(editorState.getCurrentContent());

            return (
              <Typography.Paragraph
                style={{
                  maxWidth: 1000,
                }}
                ellipsis={{ rows: 3 }}
              >
                {contentRaw.blocks.map((bl) => bl.text).join("\n")}
              </Typography.Paragraph>
            );
          } catch (error) {
            return <Typography.Paragraph ellipsis={{ rows: 3 }} />;
          }
        },
      },
      {
        width: 200,
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.action)}
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record) => (
          <Row>
            <Tooltip title={formatMessage(messages.edit)}>
              <i
                className="fa fa-edit"
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  this.setState({
                    currentItem: record,
                    visibleModalItem: true,
                  });
                }}
              />
            </Tooltip>
            &ensp;&ensp;|&ensp;&ensp;
            <Tooltip title={formatMessage(messages.delete)}>
              <i
                className="fa fa-trash"
                style={{ color: "red" }}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  Modal.confirm({
                    autoFocusButton: null,
                    title: formatMessage(messages.confirmDeleteHandbook),
                    okText: formatMessage(messages.agree),
                    okType: "danger",
                    width: 450,
                    cancelText: formatMessage(messages.cancel),
                    onOk: () => {
                      this.props.dispatch(
                        deleteHandbook({
                          // apartment_id: record.apartment_id,
                          id: record.id,
                        })
                      );
                    },
                    onCancel() {},
                  });
                }}
              />
            </Tooltip>
          </Row>
        ),
      },
    ];

    if (
      !this.props.auth_group.checkRole([
        config.ALL_ROLE_NAME.SETTING_RESIDENT_BOOK,
      ])
    ) {
      columns.splice(columns.length - 1, 1);
      columnsExpand.splice(columnsExpand.length - 1, 1);
    }

    const expandedRowRender = (record) => {
      return (
        <Table
          columns={columnsExpand}
          dataSource={handbook.data[`cate-${record.id}`] || []}
          pagination={false}
          bordered
        />
      );
    };

    return (
      <Page inner className={styles.residentHandbookPage}>
        <div>
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={formatMessage(messages.refresh)}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={() => {
                  this.reload();
                }}
                icon="reload"
                size="large"
              />
            </Tooltip>
            <WithRole roles={[config.ALL_ROLE_NAME.SETTING_RESIDENT_BOOK]}>
              <Tooltip title={formatMessage(messages.add)}>
                <Button
                  style={{ marginRight: 10 }}
                  onClick={() => {
                    this.setState({
                      visibleModalCategory: true,
                      currentCategory: undefined,
                    });
                  }}
                  icon="plus"
                  shape="circle"
                  size="large"
                />
              </Tooltip>
            </WithRole>
          </Row>
          <Table
            rowKey="id"
            loading={category.loading || category.deleting}
            columns={columns}
            dataSource={category.data}
            bordered
            locale={{ emptyText: formatMessage(messages.noData) }}
            onChange={this.handleTableChange}
            expandedRowRender={expandedRowRender}
            expandRowByClick
            pagination={false}
          />
          <ModalCreateCategory
            visible={this.state.visibleModalCategory}
            creating={category.adding}
            setState={this.setState.bind(this)}
            currentEdit={this.state.currentCategory}
            handlerAddCategory={(values) => {
              this.props.dispatch(addCategory(values));
            }}
            handlerUpdateCategory={(values) => {
              this.props.dispatch(editCategory(values));
            }}
          />
          <ModalCreateHandbookItem
            visible={this.state.visibleModalItem}
            creating={handbook.adding}
            setState={this.setState.bind(this)}
            currentEdit={this.state.currentItem}
            handlerAddHandbookItem={(values) => {
              this.props.dispatch(
                addHandbook({
                  ...values,
                  post_category_id: this.state.currentCategory,
                })
              );
            }}
            handlerUpdateHandbookItem={(values) => {
              this.props.dispatch(editHandbook(values));
            }}
          />
        </div>
      </Page>
    );
  }
}

ResidentHandbook.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  residentHandbook: makeSelectResidentHandbook(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "residentHandbook", reducer });
const withSaga = injectSaga({ key: "residentHandbook", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ResidentHandbook));
